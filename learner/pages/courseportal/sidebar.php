<?php
// Example static sidebar; replace with dynamic content as needed
$modules = [
  ['id' => 1, 'title' => 'Introduction'],
  ['id' => 2, 'title' => 'Module 1: Basics'],
  ['id' => 3, 'title' => 'Module 2: Advanced'],
];
?>
<aside class="course-sidebar">
  <ul>
    <?php foreach ($modules as $module): ?>
      <li data-module-id="<?= $module['id'] ?>" class="sidebar-item"><?= $module['title'] ?></li>
    <?php endforeach; ?>
  </ul>
</aside>
