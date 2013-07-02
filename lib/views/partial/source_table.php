<table class="table table-hover">
  
  <tr>
    <th>First Author</th>
    <th>Year</th>
    <th>PMID</th>
    <th>Title</th>
  </tr>

  <?php foreach ($rows as $row): ?>
  <tr>
    <td><?= html_esc($row->first_author) ?></td>
    <td><?= html_esc($row->pub_year) ?></td>
    <td><?= html_esc($row->pmid) ?></td>
    <td><a href="<?= href("/edit/$row->id") ?>"><?= html_esc($row->title) ?></a></td>
  </tr>
  <?php endforeach; ?>
  
</table>