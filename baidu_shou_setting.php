<?php
if (!defined('EMLOG_ROOT')) {
	die('err');
}
function plugin_setting_view() {
	$plugin_storage = Storage::getInstance('baidu_shou');
	$baidu_token = $plugin_storage->getValue('baidu_token');
	?>
	<?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">修改成功</div>
	<?php endif; ?>

<div class="card shadow mb-4 mt-2">
        <div class="card-body">
            <form method="post">
                <div class="form-group">
					<p>请输入您的百度token</p>
					<input name="baidu_token"  class="form-control" value="<?php echo $baidu_token; ?>">
					<hr/>
					<input type="submit" class="btn btn-success btn-sm" value="提交更改"/>
				</div>
			</form>
        </div>
</div>





<?php
}
if (!empty($_POST)) {
	$plugin_storages = Storage::getInstance('baidu_shou');
	$plugin_storages->setValue('baidu_token', addslashes($_POST["baidu_token"]));
	header('Location:./plugin.php?plugin=baidu_shou&success=1');
}

