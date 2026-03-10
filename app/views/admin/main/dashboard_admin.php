<?php
/**
 * Dashboard overview view for admin panel
 */
?>

<!-- Main -->
<main class="main">
  <!-- Header -->
  <div class="page-header">
    <div>
      <h1>Dashboard Overview</h1>
      <p>Monitoring performance across all channels</p>
    </div>
    <div class="last-update">Last updated: Today, 10:45 AM</div>
  </div>

  <!-- Stats -->
  <div class="stats">
    <div class="card">
      <p>Total Article Views</p>
      <h2>12.450</h2>
      <span class="badge green">+12.5%</span>
    </div>

    <div class="card">
      <p>Published Articles</p>
      <h2>1</h2>
      <span class="badge green">+3</span>
    </div>

    <div class="card">
      <p>Avg. Read Time</p>
      <h2>4:22m</h2>
      <span class="badge red">-0.4%</span>
    </div>

    <div class="card">
      <p>Active Editors</p>
      <h2>12</h2>
      <span class="badge gray">0</span>
    </div>
  </div>

  <!-- Content -->
  <div class="content-grid">
    <!-- Chart -->
    <div class="card large">
      <h3>Traffic Trends (Last 7 Days)</h3>
      <div class="fake-chart"></div>
    </div>

    <!-- Top stories -->
    <div class="card">
      <h3>Top Performing Stories</h3>
      <div class="story">
        <h4>New Policy Reform Impacts Small Businesses...</h4>
        <p>Economy • Sarah Jenkins</p>
        <span class="views">12.4k</span>
      </div>
      <a class="report-link">View full report</a>
    </div>
  </div>
</main>
