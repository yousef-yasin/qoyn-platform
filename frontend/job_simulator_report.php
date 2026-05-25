<?php
session_start();
if (!isset($_SESSION["user_id"])) {
  header("Location: login.html");
  exit;
}

$simulation_id = isset($_GET["simulation_id"]) ? (int)$_GET["simulation_id"] : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Job Simulator Report</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
  :root{
    --navy:#0A2E5D;
    --navy-2:#123c75;
    --navy-3:#1B5FAE;
    --yellow:#FFC24A;
    --yellow-2:#ffd977;
    --bg:#f6f8fb;
    --bg-soft:#eef5ff;
    --card:#ffffff;
    --text:#0f172a;
    --muted:#64748b;
    --line:#dfe8f5;
    --shadow:0 20px 50px rgba(10,46,93,.10);
    --shadow-soft:0 12px 28px rgba(15,23,42,.07);
    --radius-xl:28px;
    --radius-lg:22px;
    --radius-md:16px;
  }

  *{
    box-sizing:border-box;
  }

  html{
    scroll-behavior:smooth;
  }

  body{
    font-family:"Poppins", Arial, sans-serif;
    background:
      radial-gradient(circle at 10% 10%, rgba(255,194,74,.14), transparent 22%),
      radial-gradient(circle at 90% 12%, rgba(10,46,93,.08), transparent 24%),
      linear-gradient(180deg, #f8fbff 0%, #f3f7fc 48%, #eef3f9 100%);
    margin:0;
    padding:28px;
    color:var(--text);
    min-height:100vh;
  }

  .wrap{
    max-width:1140px;
    margin:auto;
    background:rgba(255,255,255,.92);
    backdrop-filter:blur(10px);
    -webkit-backdrop-filter:blur(10px);
    border-radius:30px;
    padding:30px;
    box-shadow:var(--shadow);
    border:1px solid rgba(255,255,255,.75);
    position:relative;
    overflow:hidden;
  }

  .wrap::before{
    content:"";
    position:absolute;
    top:-50px;
    right:-50px;
    width:180px;
    height:180px;
    border-radius:50%;
    background:radial-gradient(circle, rgba(255,194,74,.18), transparent 68%);
    pointer-events:none;
  }

  .wrap::after{
    content:"";
    position:absolute;
    bottom:-60px;
    left:-40px;
    width:180px;
    height:180px;
    border-radius:50%;
    background:radial-gradient(circle, rgba(10,46,93,.08), transparent 68%);
    pointer-events:none;
  }

  h1{
    margin:0 0 20px;
    color:var(--navy);
    font-family:"Montserrat", Arial, sans-serif;
    font-size:clamp(28px, 4vw, 40px);
    font-weight:900;
    letter-spacing:-.6px;
    line-height:1.08;
    position:relative;
    z-index:2;
  }

  .top-box{
    background:linear-gradient(135deg, rgba(238,245,255,.95) 0%, rgba(248,251,255,.96) 100%);
    border:1px solid rgba(10,46,93,.08);
    border-radius:22px;
    padding:22px;
    margin-bottom:22px;
    box-shadow:var(--shadow-soft);
    position:relative;
    overflow:hidden;
  }

  .top-box::before{
    content:"";
    position:absolute;
    right:-22px;
    top:-22px;
    width:110px;
    height:110px;
    border-radius:24px;
    background:rgba(255,194,74,.14);
    transform:rotate(20deg);
    pointer-events:none;
  }

  .grid{
    display:grid;
    grid-template-columns:repeat(2, minmax(0,1fr));
    gap:18px;
    position:relative;
    z-index:2;
  }

  .card{
    background:linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
    border-radius:18px;
    padding:20px;
    border:1px solid var(--line);
    box-shadow:0 8px 20px rgba(15,23,42,.05);
    transition:transform .18s ease, box-shadow .18s ease, border-color .18s ease;
  }

  .card:hover{
    transform:translateY(-3px);
    box-shadow:0 16px 30px rgba(15,23,42,.08);
    border-color:rgba(10,46,93,.14);
  }

  .card h3{
    margin:0 0 14px;
    color:var(--navy);
    font-family:"Montserrat", Arial, sans-serif;
    font-size:18px;
    font-weight:800;
    letter-spacing:-.2px;
  }

  ul{
    margin:8px 0 0 20px;
    padding:0;
  }

  li{
    margin-bottom:8px;
    line-height:1.8;
    color:#1e293b;
  }

  li:last-child{
    margin-bottom:0;
  }

  .muted{
    color:var(--muted);
    font-size:14px;
    line-height:1.8;
    margin:6px 0 0;
  }

  .score{
    font-size:40px;
    font-weight:900;
    font-family:"Montserrat", Arial, sans-serif;
    color:var(--navy);
    margin-bottom:10px;
    line-height:1;
    letter-spacing:-.8px;
  }

  .verdict{
    display:inline-flex;
    align-items:center;
    gap:8px;
    min-height:42px;
    padding:10px 16px;
    border-radius:999px;
    font-size:18px;
    font-weight:800;
    color:#111;
    background:rgba(255,255,255,.72);
    border:1px solid rgba(10,46,93,.08);
    box-shadow:0 8px 18px rgba(15,23,42,.04);
    margin-bottom:10px;
  }

  .back{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:8px;
    margin-top:22px;
    text-decoration:none;
    background:linear-gradient(135deg, var(--navy) 0%, var(--navy-3) 100%);
    color:#fff;
    padding:13px 20px;
    border-radius:14px;
    font-weight:800;
    font-size:14px;
    box-shadow:0 16px 34px rgba(10,46,93,.18);
    transition:transform .18s ease, box-shadow .18s ease, filter .18s ease;
    position:relative;
    z-index:2;
  }

  .back:hover{
    transform:translateY(-2px);
    box-shadow:0 22px 40px rgba(10,46,93,.22);
    filter:brightness(1.03);
  }

  #report{
    position:relative;
    z-index:2;
  }

  #report > p{
    margin:0;
    padding:20px;
    border-radius:16px;
    background:#fff;
    border:1px dashed rgba(10,46,93,.16);
    color:var(--muted);
    line-height:1.8;
  }

  @media (max-width: 860px){
    body{
      padding:18px;
    }

    .wrap{
      padding:22px;
      border-radius:24px;
    }

    .grid{
      grid-template-columns:1fr;
    }

    .score{
      font-size:34px;
    }
  }

  @media (max-width: 560px){
    body{
      padding:14px;
    }

    .wrap{
      padding:18px;
      border-radius:20px;
    }

    h1{
      font-size:30px;
    }

    .top-box{
      padding:18px;
      border-radius:18px;
    }

    .card{
      padding:16px;
      border-radius:16px;
    }

    .verdict{
      width:100%;
      justify-content:center;
      text-align:center;
    }

    .back{
      width:100%;
    }
  }
</style>
</head>
<body>
  <div class="wrap">
    <h1>AI Job Readiness Report</h1>
    <div id="report">Loading report...</div>
    <a href="job_simulator.php" class="back">Back to Simulator</a>
  </div>

  <script>
    const simulationId = <?php echo (int)$simulation_id; ?>;

    async function loadReport() {
      const box = document.getElementById("report");

      if (!simulationId || simulationId <= 0) {
        box.innerHTML = "<p>Invalid simulation id.</p>";
        return;
      }

      try {
        const res = await fetch("/utbn-backend/api/job_simulator_get_report.php?simulation_id=" + simulationId, {
          credentials: "include"
        });

        const data = await res.json();

        if (!data.ok) {
          box.innerHTML = "<p>Failed to load report: " + (data.error || "unknown error") + "</p>";
          return;
        }

        const sim = data.simulation || {};
        const cv = data.cv_analysis || {};
        const project = data.project_analysis || {};
        const scores = data.scores || {};
        const roadmap = data.roadmap || {};

        const skills = cv.skills || [];
        const cvStrengths = cv.strengths || [];
        const cvWeaknesses = cv.weaknesses || [];
        const projectStrengths = project.strengths || [];
        const projectWeaknesses = project.weaknesses || [];
        const missingSkills = roadmap.missing_skills || [];
        const actions = roadmap.recommended_actions || [];

        box.innerHTML = `
          <div class="top-box">
            <div class="score">Final Score: ${scores.final_score ?? sim.final_score ?? 0}</div>
            <div class="verdict">Verdict: ${sim.verdict || "Unknown"}</div>
            <p class="muted">Role: ${sim.role_key || "-"}</p>
            <p class="muted">Status: ${sim.status || "-"}</p>
          </div>

          <div class="grid">
            <div class="card">
              <h3>Scores Breakdown</h3>
              <p><strong>CV Score:</strong> ${scores.cv_score ?? 0}</p>
              <p><strong>Project Score:</strong> ${scores.project_score ?? 0}</p>
              <p><strong>Skill Match Score:</strong> ${scores.skill_match_score ?? 0}</p>
              <p><strong>Progress Score:</strong> ${scores.progress_score ?? 0}</p>
            </div>

            <div class="card">
              <h3>Detected Skills</h3>
              <ul>${skills.map(s => `<li>${s}</li>`).join("")}</ul>
            </div>

            <div class="card">
              <h3>CV Strengths</h3>
              <ul>${cvStrengths.map(s => `<li>${s}</li>`).join("")}</ul>
            </div>

            <div class="card">
              <h3>CV Weaknesses</h3>
              <ul>${cvWeaknesses.map(w => `<li>${w}</li>`).join("")}</ul>
            </div>

            <div class="card">
              <h3>Project Strengths</h3>
              <ul>${projectStrengths.map(s => `<li>${s}</li>`).join("")}</ul>
            </div>

            <div class="card">
              <h3>Project Weaknesses</h3>
              <ul>${projectWeaknesses.map(w => `<li>${w}</li>`).join("")}</ul>
            </div>

            <div class="card">
              <h3>Missing Skills</h3>
              <ul>${missingSkills.map(s => `<li>${s}</li>`).join("")}</ul>
            </div>

            <div class="card">
              <h3>Recommended Actions</h3>
              <ul>${actions.map(a => `<li>${a}</li>`).join("")}</ul>
            </div>
          </div>
        `;
      } catch (err) {
        console.error(err);
        box.innerHTML = "<p>Unexpected error while loading report.</p>";
      }
    }

    loadReport();
  </script>
</body>
</html>