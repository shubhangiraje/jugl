<?php
/* Smarty version 3.1.33, created on 2018-11-22 10:48:31
  from '/var/www/jugl/views/admin-site/error.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_5bf65f4f28b777_90762964',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '2e6941ed0bd896b09ced8e3d65abefc531881335' => 
    array (
      0 => '/var/www/jugl/views/admin-site/error.tpl',
      1 => 1538142780,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5bf65f4f28b777_90762964 (Smarty_Internal_Template $_smarty_tpl) {
$_tmp_array = isset($_smarty_tpl->tpl_vars['this']) ? $_smarty_tpl->tpl_vars['this']->value : array();
if (!is_array($_tmp_array) || $_tmp_array instanceof ArrayAccess) {
settype($_tmp_array, 'array');
}
$_tmp_array['title'] = $_smarty_tpl->tpl_vars['name']->value;
$_smarty_tpl->_assignInScope('this', $_tmp_array);?>
<div class="site-error">

    <h1><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['this']->value['title'], ENT_QUOTES, 'UTF-8', true);?>
</h1>

    <div class="alert alert-danger">
        <?php echo nl2br(htmlspecialchars($_smarty_tpl->tpl_vars['message']->value, ENT_QUOTES, 'UTF-8', true));?>

    </div>

    <p>
        The above error occurred while the Web server was processing your request.
    </p>
    <p>
        Please contact us if you think this is a server error. Thank you.
    </p>

</div>
<?php }
}
