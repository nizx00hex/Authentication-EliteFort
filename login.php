<?php
require_once __DIR__ . '/libs/init.php';

?>
<?= loadTemplates("_head")?>
<body>

    <?= loadTemplates("_loginForm") ?>

    <script>
      function closeAlert(alertId) {
          const alertBox = document.getElementById(alertId);

          if (!alertBox || alertBox.classList.contains("hide")) {
              return;
          }

          alertBox.classList.add("hide");

          setTimeout(() => {
              alertBox.remove();
          }, 300);
      }

      document.addEventListener("DOMContentLoaded", function () {
          document.querySelectorAll(".alert").forEach(function (alertBox) {
              setTimeout(function () {
                  closeAlert(alertBox.id);
              }, 5000);
          });
      });
  </script>
</body>

</html>