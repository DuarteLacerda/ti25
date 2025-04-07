document.addEventListener("DOMContentLoaded", function () {
  function updateClocks() {
    const lisbonTime = new Date().toLocaleString("en-GB", {
      timeZone: "Europe/Lisbon",
      hour: "2-digit",
      minute: "2-digit",
      second: "2-digit",
      hourCycle: "h23",
    });

    document.getElementById("lisbon-time").textContent = lisbonTime;
  }

  setInterval(updateClocks, 1000);
  updateClocks();
});
