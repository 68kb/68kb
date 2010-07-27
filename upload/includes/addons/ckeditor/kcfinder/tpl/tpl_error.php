<root>
<?php IF (is_array($message)): ?>
<?php   FOREACH ($message as $msg): ?>
<error><![CDATA[<?php echo $msg ?>]]></error>
<?php   ENDFOREACH ?>
<?PHP ELSE: ?>
<error><![CDATA[<?php echo $message ?>]]></error>
<?PHP ENDIF ?>
</root>