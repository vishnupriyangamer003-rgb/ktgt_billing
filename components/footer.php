    </main>
<?php if (!$embed) { ?>
    <footer class="app-footer">
      <span>© 2026 <a href="profile.php">Knock The Globe Technologies</a>. All rights reserved.</span>
      <span>4th Cross Rd, Vaiyapuri Nagar, Karur, Tamil Nadu 639002 &nbsp;&middot;&nbsp; 72001 23452 &nbsp;&middot;&nbsp; www.knocktheglobe.com</span>
    </footer>
  </div>
</div>
<?php } ?>

<?php if (!empty($pageModals)) { echo $pageModals; } ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/app.js"></script>
<?php if (!empty($pageScript)) { ?>
<script>
<?php echo $pageScript; ?>
</script>
<?php } ?>
</body>
</html>
