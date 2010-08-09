<h2>{lang:lang_glossary}</h2>

<table border="0" class="glossary_terms" width="100%">
	<tr>
		<td><a href="<?php echo site_url('glossary/term/sym'); ?>">#</a></td>
		<?php foreach($letter as $row): ?>
			<td><a href="<?php echo site_url('glossary/term/'.$row); ?>"><?php echo strtoupper($row); ?></a></td>
		<?php endforeach; ?>
	</tr>
</table>

<?php foreach($glossary->result() as $row): ?>
<dl>
	<dt><a name="<?php echo $row->g_term; ?>"></a><?php echo $row->g_term; ?></dt>
	<dd><?php echo $row->g_definition; ?></dd>
</dl>	
<?php endforeach; ?>