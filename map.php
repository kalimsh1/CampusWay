<?php include 'includes/header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Campus Map</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #f9faff 0%, #f0ebff 100%);
      font-family: 'Segoe UI', sans-serif;
      min-height: 100vh;
      overflow-x: hidden;
      display: flex;
      flex-direction: column;
    }

    .map-container {
      flex: 1;
      position: relative;
      width: 100%;
      height: calc(100vh - 120px); /* Adjust this value based on your header/footer height */
    }

    iframe {
      border: none;
      width: 100%;
      height: 100%;
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
    }

    @media (max-width: 600px) {
      .map-container {
        height: calc(100vh - 100px); /* Slightly smaller on mobile */
      }
    }
  </style>
</head>
<body>

<div class="map-container">
  <iframe 
    title="Mappedin Map"
    src="https://app.mappedin.com/map/6806529c72e5eb000bf9dc97?embedded=true"
    allowfullscreen>
  </iframe>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>
