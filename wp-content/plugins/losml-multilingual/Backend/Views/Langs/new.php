<div class="">
    <form>
        <div>
            <label>code: </label>
            <span><input type="text" name="code" value="<?php echo $data->code; ?>"></span>
        </div>
        <div>
            <label>name: </label>
            <span><input type="text" name="name" value="<?php echo $data->name; ?>"></span>
        </div>
        <div>
            <label>host: </label>
            <span><input type="text" name="host" value="<?php echo $data->host; ?>"></span>
        </div>
        <div>
            <label>is_enabled: </label>
            <span>
                <input type="radio" name="is_enabled" value="1" <?php echo $data->is_enabled == 1 || ! $data->is_enabled ? 'checked' : ''; ?>>是
                <input type="radio" name="is_enabled" value="2" <?php echo $data->is_enabled == 2 ? 'checked' : ''; ?>>否
            </span>
        </div>
        <input type="hidden" name="nonce" value="<?php echo $nonce; ?>">
        <input type="hidden" name="action" value="losml_add_langs">
        <input type="hidden" name="id" value="<?php echo $data->id; ?>">
        <p class="submit"><input type="button" id="losml-btn-save" class="button-primary" name="save" value="Save"></p>
    </form>
</div>
<div class="big-loading" id="big-loading"></div>