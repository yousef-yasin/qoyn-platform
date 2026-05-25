import requests
from urllib.parse import urlparse

def parse_github_repo(url: str):
    try:
        path = urlparse(url).path.strip("/")
        parts = path.split("/")
        if len(parts) >= 2:
            return parts[0], parts[1]
    except Exception:
        pass
    return None, None

def score_project(project_url: str, role_key: str):
    if not project_url:
        return {
            "project_score": 20,
            "strengths": [],
            "weaknesses": ["No project link provided"]
        }

    strengths = []
    weaknesses = []
    score = 20

    if "github.com" not in project_url.lower():
        return {
            "project_score": 35,
            "strengths": ["Project link provided"],
            "weaknesses": ["Project is not hosted on GitHub"]
        }

    score += 15
    strengths.append("Project link provided")
    strengths.append("GitHub project detected")

    owner, repo = parse_github_repo(project_url)
    if not owner or not repo:
        return {
            "project_score": score,
            "strengths": strengths,
            "weaknesses": ["Invalid GitHub repository URL"]
        }

    repo_api = f"https://api.github.com/repos/{owner}/{repo}"
    readme_api = f"https://api.github.com/repos/{owner}/{repo}/readme"
    commits_api = f"https://api.github.com/repos/{owner}/{repo}/commits"

    try:
        repo_res = requests.get(repo_api, timeout=20)
        if repo_res.status_code == 200:
            repo_data = repo_res.json()

            if repo_data.get("description"):
                score += 10
                strengths.append("Repository has description")
            else:
                weaknesses.append("Repository description missing")

            if repo_data.get("stargazers_count", 0) >= 0:
                score += 5

            if repo_data.get("language"):
                score += 10
                strengths.append(f"Primary language detected: {repo_data.get('language')}")

        else:
            weaknesses.append("GitHub repo metadata could not be fetched")

        readme_res = requests.get(readme_api, timeout=20)
        if readme_res.status_code == 200:
            score += 15
            strengths.append("README detected")
        else:
            weaknesses.append("README missing")

        commits_res = requests.get(commits_api, timeout=20)
        if commits_res.status_code == 200:
            commits = commits_res.json()
            if isinstance(commits, list):
                c = len(commits)
                if c >= 3:
                    score += 15
                    strengths.append("Repository has commit history")
                else:
                    weaknesses.append("Repository has very few commits")
        else:
            weaknesses.append("Commit history unavailable")

    except Exception as e:
        weaknesses.append(f"GitHub API fetch failed: {str(e)[:80]}")

    return {
        "project_score": min(score, 100),
        "strengths": strengths,
        "weaknesses": weaknesses
    }