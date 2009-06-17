<ul>
<?php foreach ($data['rows'] as $row) : ?>
    <li><?php echo $row->firstname; ?> <?php echo $row->lastname; ?></li>
<?php  endforeach; ?>
</ul>
