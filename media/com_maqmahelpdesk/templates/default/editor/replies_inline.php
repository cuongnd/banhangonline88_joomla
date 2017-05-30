<?php foreach($replies as $reply):?>
<li>
    <a href="javascript:;" class="redactor_reply_link"><?php echo $reply->subject;?></a>
    <div class="redactor_reply" style="display: none;"><?php echo $reply->answer;?></div>
</li>
<?php endforeach;?>