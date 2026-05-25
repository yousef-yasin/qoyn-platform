document.addEventListener("DOMContentLoaded", () => {
  const startBtn = document.getElementById("startBtn");
  const roleKey = document.getElementById("role_key");
  const cvFile = document.getElementById("cv_file");
  const githubUrl = document.getElementById("github_url");
  const resultBox = document.getElementById("result");

  startBtn.addEventListener("click", async () => {
    try {
      resultBox.style.display = "block";
      resultBox.innerHTML = "Starting simulation...";

      const startRes = await fetch("/utbn-backend/api/job_simulator_start.php", {
        method: "POST",
        credentials: "include",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify({
          role_key: roleKey.value
        })
      });

      const startData = await startRes.json();

      if (!startData.ok) {
        resultBox.innerHTML = "Start failed: " + (startData.error || "unknown error");
        return;
      }

      const simulationId = startData.simulation_id;

      if (cvFile.files.length > 0) {
        resultBox.innerHTML = "Uploading CV...";

        const fd = new FormData();
        fd.append("simulation_id", simulationId);
        fd.append("cv_file", cvFile.files[0]);

        const uploadRes = await fetch("/utbn-backend/api/job_simulator_upload_cv.php", {
          method: "POST",
          credentials: "include",
          body: fd
        });

        const uploadData = await uploadRes.json();

        if (!uploadData.ok) {
          resultBox.innerHTML = "CV upload failed: " + (uploadData.error || "unknown error");
          return;
        }
      }

      resultBox.innerHTML = "Saving project link...";

      const projRes = await fetch("/utbn-backend/api/job_simulator_add_project.php", {
        method: "POST",
        credentials: "include",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify({
          simulation_id: simulationId,
          project_url: githubUrl.value
        })
      });

      const projData = await projRes.json();

      if (!projData.ok) {
        resultBox.innerHTML = "Project save failed: " + (projData.error || "unknown error");
        return;
      }

      resultBox.innerHTML = "Running AI analysis...";

      const analyzeRes = await fetch("/utbn-backend/api/job_simulator_analyze.php", {
        method: "POST",
        credentials: "include",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify({
          simulation_id: simulationId
        })
      });

      const analyzeData = await analyzeRes.json();

      if (!analyzeData.ok) {
        resultBox.innerHTML = "Analysis failed: " + (analyzeData.error || "unknown error");
        return;
      }

      window.location.href = "job_simulator_report.php?simulation_id=" + simulationId;

    } catch (err) {
      console.error(err);
      resultBox.style.display = "block";
      resultBox.innerHTML = "Unexpected error happened.";
    }
  });
});