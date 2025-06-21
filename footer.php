</div> <!-- container -->

<div class="position-fixed bottom-0 end-0 p-3 text-muted small text-end" style="z-index: 1030;">
  <div>Czas lokalny: <span id="clock_footer">--:--:--</span></div>
  <!-- Tu można wstawić liczniki odwiedzin itp. -->
</div>

<script>
  function updateClockFooter() {
    const now = new Date();
    const h = String(now.getHours()).padStart(2, '0');
    const m = String(now.getMinutes()).padStart(2, '0');
    const s = String(now.getSeconds()).padStart(2, '0');
    document.getElementById('clock_footer').textContent = `${h}:${m}:${s}`;
  }
  setInterval(updateClockFooter, 1000);
  updateClockFooter();
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
