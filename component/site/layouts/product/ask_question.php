<?php
/**
 * @package     RedSHOP.Frontend
 * @subpackage  Template
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

$app = JFactory::getApplication();
JHTML::_('behavior.tooltip');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');

$redTemplate = new Redtemplate;
$user = JFactory::getUser();
$form = $displayData['form'];

if ($user->id)
{
	$form->setValue('your_name', null, $form->getValue('your_name', null, $user->name));
	$form->setValue('your_email', null, $form->getValue('your_email', null, $user->email));
}

$Itemid = $app->input->getInt('Itemid', 0);
$pid = $app->input->getInt('pid', 0);
$category_id = $app->input->getInt('category_id', 0);
$template = $redTemplate->getTemplate('ask_question_template');

if (count($template) > 0 && $template[0]->template_desc != "")
{
	$template_desc = $template[0]->template_desc;
}
else
{
	$template_desc = '<table border="0"><tbody><tr><td>{user_name_lbl}</td><td>{user_name}</td></tr><tr><td>{user_email_lbl}</td><td>{user_email}</td></tr><tr><td>{user_question_lbl}</td><td>{user_question}</td></tr><tr><td></td><td>{send_button}</td></tr></tbody></table>';
}

?>
<script type="text/javascript" language="javascript">
	questionSubmitButton = function (task) {
		var askQuestionForm = document.getElementById('askQuestionForm');

		if (document.formvalidator.isValid(askQuestionForm)) {
			Joomla.submitform(task, askQuestionForm);
		}
	}
</script>
<form name="askQuestionForm" action="<?php echo JRoute::_('index.php?option=com_redshop'); ?>" method="post"
	  id="askQuestionForm" class="form-validate form-vertical">
	<?php
	$template_desc = str_replace('{user_name_lbl}', $form->getLabel('your_name'), $template_desc);
	$template_desc = str_replace('{user_email_lbl}', $form->getLabel('your_email'), $template_desc);
	$template_desc = str_replace('{user_question_lbl}', $form->getLabel('your_question'), $template_desc);
	$template_desc = str_replace('{user_telephone_lbl}', $form->getLabel('telephone'), $template_desc);
	$template_desc = str_replace('{user_address_lbl}', $form->getLabel('address'), $template_desc);
	$template_desc = str_replace('{user_name}', $form->getInput('your_name'), $template_desc);
	$template_desc = str_replace('{user_email}', $form->getInput('your_email'), $template_desc);
	$template_desc = str_replace('{user_question}', $form->getInput('your_question'), $template_desc);
	$template_desc = str_replace('{user_address}', $form->getInput('address'), $template_desc);
	$template_desc = str_replace('{user_telephone}', $form->getInput('telephone'), $template_desc);
	$template_desc = str_replace('{send_button}', '<input type="submit" class="btn" value="' . JText::_('COM_REDSHOP_SEND') . '" onclick="questionSubmitButton(\'ask_question.submit\')" />', $template_desc);

	$captcha = '';
	$captchaLbl = '';

	if (SHOW_CAPTCHA && $user->guest)
	{
		$captcha = '<div class="questionCaptcha">'
			. '<div class="captchaImage"><img src="' . JURI::base(true) . '/index.php?tmpl=component&option=com_redshop&view=registration&task=captcha&captcha=security_code&width=100&height=40&characters=5" /></div>'
			. '<div class="captchaField"><input class="inputbox required" required="required" id="jform_security_code" name="jform[security_code]" type="text" /></div>'
			. '</div>';
		$captchaLbl = '<label for="jform_security_code" id="jform_security_code-lbl" class="required">' . JText::_('COM_REDSHOP_CAPTCHA') . '<span class="star">&nbsp;*</span></label>';
	}

	$template_desc = str_replace('{captcha}', $captcha, $template_desc);
	$template_desc = str_replace('{captcha_lbl}', $captchaLbl, $template_desc);

	echo eval('?>' . $template_desc . '<?php ');
	?>
	<input type="hidden" name="pid" id="pid" value="<?php echo $pid; ?>"/>
	<input type="hidden" name="task" id="task" value=""/>
	<input type="hidden" name="ask" value="<?php echo $displayData['ask']; ?>"/>
	<input type="hidden" name="category_id" id="category_id" value="<?php echo $category_id; ?>"/>
	<input type="hidden" name="Itemid" id="Itemid" value="<?php echo $Itemid; ?>"/>
	<?php echo JHtml::_('form.token'); ?>
</form>