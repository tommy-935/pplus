<p class="wrap"><?php echo __('LOSML MULTI LANGUAGE LIST', 'losml'); ?> 
<a href="<?php echo admin_url('admin.php?page=losml_new_lang'); ?>" class="page-title-action">Add New</a>
</p>
<table class="wp-list-table widefat fixed striped posts">
    <thead>
        <tr><th>id</th> <th>code</th><th>name</th><th>host</th><th>is_enabled</th><th>action</th></tr>    
    </thead>
    <tbody id="losml-p-list">
        <?php foreach($list as $obj){ ?>
        <tr>
            <td><?php echo $obj->id; ?></td>
            <td><?php echo $obj->code; ?></td>
            <td><?php echo $obj->name; ?></td>
            <td><?php echo $obj->host; ?></td>
            <td><?php echo $obj->is_enabled == 1 ? 'yes' : 'no'; ?></td>
            <td>
                <a href="<?php echo admin_url('admin.php?page=losml_new_lang&id=' . $obj->id); ?>" class="losml-lang-edit-btn">Edit</a>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>