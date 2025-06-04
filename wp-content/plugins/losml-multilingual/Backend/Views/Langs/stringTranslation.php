<div class="wrap">
    <div class="search">
        <form method="GET" action="">
            <span>domain: </span>
            <select class="" name="domain">
                <option value="">==</option>
                <?php foreach($domain_list as $obj){ ?>
                    <?php 
                        $selected = '';
                        if($domain == $obj->domain){
                            $selected = 'selected';
                        }
                    ?>
                    <option value="<?php echo $obj->domain; ?>" <?php echo $selected; ?>><?php echo $obj->domain; ?></option>
                <?php } ?>
            </select>
            <span>keyword: </span>
            <input type="text" name="keyword" value="<?php echo $keyword; ?>">
            <input type="hidden" name="page" value="losml_string_translations">
            <input class="button" type="submit" value="search">
        </form>
    </div>
    <table class="widefat">
        <thead>
            <tr>
                <th style="width: 20%;">domain</th>
                <th>text</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($list as $obj){ ?>
            <tr>
                <td><?php echo $obj->domain; ?></td>
                <td>
                    <div><?php echo $obj->text; ?></div>
                    <div><a class="losml-toggle-trans" href="javascript:void(0);">translations</a></div>
                    <div class="losml-string-trans-box">
                        <?php 
                           
                            foreach($lang_data as $lang_obj){ 
                                if($lang_obj->code == 'en'){
                                    continue;
                                }
                        ?>
                                <div class="losml-string-item">
                                    <span><?php echo $lang_obj->name; ?></span>
                                    <?php
                                        $trans = isset($trans_data[$lang_obj->id]) ? $trans_data[$lang_obj->id] : [];

                                        $tran = isset($trans[$obj->id]) ? $trans[$obj->id] : false;
                                        if($tran){
                                            $text = $tran->trans_text;
                                        }else{
                                            $text = '';
                                        }
                                    ?>
                                    <textarea class="trans_text" name="trans_text"><?php echo $text; ?></textarea>
                                    <div><input type="button" value="save" data-lang-id="<?php echo $lang_obj->id; ?>" data-id="<?php echo $tran->id; ?>" data-string-id="<?php echo $obj->id; ?>" class="button losml-btn-string-save"></div>
                                </div>
                            <?php } ?>
                    </div>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<div class="big-loading" id="big-loading"></div>