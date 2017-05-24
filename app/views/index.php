<?php echo $this->render('head'); ?>
        <h1><?php echo $title; ?></h1>
        <table id="animals">
            <thead>
                <tr>
<?php foreach($columns as $column): ?>
                    <th><?php echo ucfirst($column); ?></th>
<?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
<?php foreach($rows as $row): ?>
                <tr>
<?php foreach($columns as $column): ?>
                    <td><?php echo $row->$column; ?></td>
<?php endforeach; ?>
                </tr>
<?php endforeach; ?>
            </tbody>
        </table>
        <hr>
        <a href="/animals">Create an Animal</a> | <a href="/logout">Log Out</a>
<?php echo $this->render('foot'); ?>