document.addEventListener("DOMContentLoaded", function () {
    function updateClocks() {
        const lisbonTime = new Date().toLocaleString("en-GB", {
            timeZone: "Europe/Lisbon",
            hour: "2-digit",
            minute: "2-digit",
            second: "2-digit",
            hourCycle: "h23",
        });
        const newYorkTime = new Date().toLocaleString("en-US", {
            timeZone: "America/New_York",
            hour: "2-digit",
            minute: "2-digit",
            second: "2-digit",
            hourCycle: "h23",
        });
        const tokyoTime = new Date().toLocaleString("en-JP", {
            timeZone: "Asia/Tokyo",
            hour: "2-digit",
            minute: "2-digit",
            second: "2-digit",
            hourCycle: "h23",
        });

        document.getElementById("lisbon-time").textContent = lisbonTime;
        document.getElementById("newyork-time").textContent = newYorkTime;
        document.getElementById("tokyo-time").textContent = tokyoTime;
    }

    setInterval(updateClocks, 1000);
    updateClocks();
});
