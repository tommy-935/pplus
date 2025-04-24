<?php
/*
create database test1 default character set utf8mb4 collate utf8mb4_unicode_ci;
use test1;
create table test (
    id int(11) not null auto_increment,
    content varchar(10000) not null default "",
    primary key(id)
)ENGINE=Innodb;
*/
if($_POST){
    $content = trim(addslashes($_POST['content']));
    if(! $content){
        die;
    }
    $db = new mysqli('localhost', 'root', 'root', 'test1');
    $sql = 'insert into test (content) values ("' . $content . '")';
    $db->query($sql);
    exit;
}
?>
<!DOCTYPE html>
<html>
<body>
<form method="POST" action="">
    <textarea name="content"></textarea>
    <button type="submit">submit</button>
</form>
</body>
</html>
