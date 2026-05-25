# grader_v2.py
from __future__ import annotations
from dataclasses import dataclass
from pathlib import Path
import subprocess, time, json, socket
from typing import Dict, Any, List, Optional
import os
import requests
import platform

# Optional CV stack
try:
    from playwright.sync_api import sync_playwright
except Exception:
    sync_playwright = None

try:
    import pytesseract
    from PIL import Image
    
except Exception:
    pytesseract = None
    Image = None


def _free_port() -> int:
    s = socket.socket()
    s.bind(("127.0.0.1", 0))
    port = s.getsockname()[1]
    s.close()
    return port


def _run(cmd: List[str], cwd: Optional[str] = None, timeout: int = 120) -> Dict[str, Any]:
    """
    Safer subprocess runner (UTF-8 output, cross-platform friendly).
    """
    try:
        env = dict(**os.environ)
        env["PYTHONIOENCODING"] = "utf-8"
        env["PYTHONUTF8"] = "1"
        env["LC_ALL"] = env.get("LC_ALL", "C.UTF-8")

        p = subprocess.run(
            cmd,
            cwd=cwd,
            capture_output=True,
            text=True,
            timeout=timeout,
            encoding="utf-8",
            errors="replace",
            env=env
        )
        return {
            "ok": p.returncode == 0,
            "code": p.returncode,
            "out": (p.stdout or "")[-4000:],
            "err": (p.stderr or "")[-4000:],
        }
    except Exception as e:
        return {"ok": False, "code": -1, "out": "", "err": str(e)}


def detect_stack(root: Path) -> str:
    if (root / "index.php").exists() or any(root.rglob("*.php")):
        return "php"
    if (root / "package.json").exists():
        return "node"
    if (root / "requirements.txt").exists() or (root / "app.py").exists():
        return "python"
    return "unknown"


def php_lint(root: Path, max_files: int = 200) -> List[Dict[str, Any]]:
    issues = []
    files = list(root.rglob("*.php"))[:max_files]
    for f in files:
        res = _run(["php", "-l", str(f)], timeout=25)
        if not res["ok"]:
            issues.append({
                "severity": "error",
                "type": "php_lint",
                "file": str(f),
                "message": (res["out"] + "\n" + res["err"]).strip()[:2000]
            })
    return issues


def _semgrep_cmd(root: Path, tmp_out: Path) -> List[str]:
    """
    Cross-platform semgrep command.
    On Windows: run through cmd to force UTF-8 codepage.
    """
    if platform.system().lower().startswith("win"):
        return [
            "cmd", "/c",
            f"chcp 65001 >NUL && semgrep --config=auto --json --output \"{tmp_out}\" \"{root}\""
        ]
    return ["semgrep", "--config=auto", "--json", "--output", str(tmp_out), str(root)]



def semgrep_scan(root: Path) -> List[Dict[str, Any]]:

    tmp_out = root / "_ai_semgrep.json"
    if tmp_out.exists():
        try:
            tmp_out.unlink()
        except Exception:
            pass

    # 1) Try semgrep from PATH
    res = _run(_semgrep_cmd(root, tmp_out), timeout=240)

    # 2) Optional fallback path on Windows (pipx)
    if not res["ok"] and platform.system().lower().startswith("win"):
        alt = r"C:\Users\User\.local\bin\semgrep.exe"
        res2 = _run([alt, "--config=auto", "--json", "--output", str(tmp_out), str(root)], timeout=240)
        if res2["ok"]:
            res = res2

    if not res["ok"]:
        return [{
            "severity": "warn",
            "type": "semgrep",
            "message": "semgrep failed or not installed",
            "detail": (res["err"] or res["out"])[:800]
        }]

    try:
        if not tmp_out.exists():
            return [{"severity": "warn", "type": "semgrep", "message": "semgrep output missing"}]

        data = json.loads(tmp_out.read_text(encoding="utf-8", errors="replace"))
        out: List[Dict[str, Any]] = []
        for r in data.get("results", [])[:200]:
            out.append({
                "severity": "warn",
                "type": "semgrep",
                "file": r.get("path"),
                "line": (r.get("start", {}) or {}).get("line"),
                "message": (r.get("extra", {}) or {}).get("message"),
                "rule": r.get("check_id"),
            })
        return out
    except Exception as e:
        return [{"severity": "warn", "type": "semgrep", "message": f"semgrep json parse failed: {e}"}]


@dataclass
class RunningServer:
    proc: subprocess.Popen
    base_url: str
    port: int
    stack: str

    def stop(self):
        try:
            self.proc.terminate()
            try:
                self.proc.wait(timeout=3)
            except Exception:
                try:
                    self.proc.kill()
                except Exception:
                    pass
        except Exception:
            pass



def start_php_server(docroot: Path) -> RunningServer:
    port = _free_port()


    proc = subprocess.Popen(
        ["php", "-S", f"127.0.0.1:{port}", "-t", str(docroot)],
        stdout=subprocess.PIPE,
        stderr=subprocess.PIPE,
        text=True,
        encoding="utf-8",
        errors="replace",
        bufsize=1,
    )
    base_url = f"http://127.0.0.1:{port}"
    time.sleep(2)
    return RunningServer(proc=proc, base_url=base_url, port=port, stack="php")


def smoke_http(base_url: str) -> Dict[str, Any]:
    out = {"base_url": base_url, "checks": []}


    paths = ["/index.php", "/"]
    for p in paths:
        try:
            r = requests.get(base_url + p, timeout=8)
            out["checks"].append({
                "path": p,
                "status": r.status_code,
                "snippet": (r.text or "")[:300]
            })
        except Exception as e:
            out["checks"].append({"path": p, "error": str(e)})

    out["ok"] = any(c.get("path") == "/index.php" and c.get("status") in (200, 301, 302) for c in out["checks"]) \
                or any(c.get("path") == "/" and c.get("status") in (200, 301, 302) for c in out["checks"])
    return out


def _maybe_configure_tesseract():
    """
    Optional Windows configuration for pytesseract.
    If not found, OCR will still be disabled safely.
    """
    if pytesseract is None:
        return

    if platform.system().lower().startswith("win"):
        win_path = r"C:\Program Files\Tesseract-OCR\tesseract.exe"
        if os.path.exists(win_path):
            pytesseract.pytesseract.tesseract_cmd = win_path


def take_screenshots_and_ocr(base_url: str, out_dir: Path) -> Dict[str, Any]:
    out = {"enabled": False, "screenshots": [], "ocr_findings": []}
    if sync_playwright is None:
        out["reason"] = "playwright not installed"
        return out

    out_dir.mkdir(parents=True, exist_ok=True)
    out["enabled"] = True

    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        page = browser.new_page()
        page.set_viewport_size({"width": 1280, "height": 720})
        try:
            # ✅ جرّب index.php ثم /
            for path, name in [("/index.php", "index.png"), ("/", "home.png")]:
                try:
                    page.goto(base_url + path, wait_until="domcontentloaded", timeout=15000)
                    time.sleep(1)
                    shot = out_dir / name
                    page.screenshot(path=str(shot), full_page=True)
                    out["screenshots"].append(str(shot))
                except Exception as e:
                    out["ocr_findings"].append({"type": "nav_error", "path": path, "message": str(e)})
        finally:
            browser.close()

    _maybe_configure_tesseract()

    if pytesseract is None or Image is None:
        out["reason_ocr"] = "tesseract/pillow not installed"
        return out

    patterns = ["warning", "undefined", "fatal", "error", "exception", "500", "sql", "notice"]
    for sp in out["screenshots"]:
        try:
            img = Image.open(sp)
            text = (pytesseract.image_to_string(img) or "").lower()
            hits = [w for w in patterns if w in text]
            if hits:
                out["ocr_findings"].append({"file": sp, "hits": hits, "sample": text[:500]})
        except Exception as e:
            out["ocr_findings"].append({"file": sp, "error": str(e)})

    return out


def find_app_root(root: Path) -> Path:

    candidates = [p.parent for p in root.rglob("index.php")]
    if not candidates:
        return root

    def score(dirp: Path) -> int:
        s = 0
        parts = [p.lower() for p in dirp.parts]
        if "public" in parts:  s += 50
        if "backend" in parts: s += 40


        if (dirp / "assets").exists(): s += 10
        if (dirp / "style.css").exists(): s += 5
        if (dirp / "my_project.php").exists(): s += 6
        if (dirp / "my_courses.php").exists(): s += 6


        try:
            depth = len(dirp.relative_to(root).parts)
        except Exception:
            depth = 999
        s += max(0, 8 - depth)
        return s

    candidates.sort(key=score, reverse=True)
    return candidates[0]


def build_evidence(project: Dict[str, Any], submission: Dict[str, Any]) -> Dict[str, Any]:
    artifact_dir_abs = (submission or {}).get("artifact_dir_abs") or ""
    root = Path(artifact_dir_abs).resolve()
    if not artifact_dir_abs or not root.exists():
        return {"ok": False, "error": "MISSING_ARTIFACT_DIR_ABS", "artifact_dir_abs": artifact_dir_abs}


    app_root = find_app_root(root)


    stack = detect_stack(app_root)

    evidence: Dict[str, Any] = {
        "ok": True,
        "stack": stack,
        "artifact_dir_abs": str(root),
        "app_root": str(app_root),
        "static_issues": [],
        "runtime": {},
        "vision": {},
    }

    # Static analysis
    if stack == "php":
        evidence["static_issues"].extend(php_lint(app_root))
    evidence["static_issues"].extend(semgrep_scan(app_root))

    # Runtime + Vision
    server: Optional[RunningServer] = None
    try:
        if stack == "php":

            server = start_php_server(app_root)

            evidence["runtime"] = smoke_http(server.base_url)
            evidence["vision"] = take_screenshots_and_ocr(server.base_url, root / "_ai_shots")
    finally:
        if server:
            server.stop()

    return evidence