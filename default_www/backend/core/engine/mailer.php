<?php

// require SpoonEmail
require_once 'spoon/email/email.php';


/**
 * BackendMailer
 *
 * This class will send mails
 *
 * @package		backend
 * @subpackage	mailer
 *
 * @author		Davy Hellemans <davy@netlash.com>
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @author		Dave Lens <dave@netlash.com>
 * @since		2.0
 */
class BackendMailer
{
	/**
	 * Adds an email to the queue.
	 *
	 * @return	void
	 * @param	string $subject
	 * @param	string $template
	 * @param	array[optional] $variables
	 * @param	string[optional] $toEmail
	 * @param	string[optional] $toName
	 * @param	string[optional] $fromEmail
	 * @param	string[optional] $fromName
	 * @param	bool[optional] $queue
	 */
	public static function addEmail($subject, $template, array $variables = null, $toEmail = null, $toName = null, $fromEmail = null, $fromName = null, $replyToEmail = null, $replyToName = null, $queue = false)
	{
		// redefine
		$subject = (string) $subject;
		$template = (string) $template;

		// set defaults
		$to = BackendModel::getSetting('core', 'mailer_to');
		$from = BackendModel::getSetting('core', 'mailer_from');
		$replyTo = BackendModel::getSetting('core', 'mailer_reply_to');

		// set recipient/sender headers
		$email['to_email'] = (empty($toEmail)) ? (string) $to[0] : $toEmail;
		$email['to_name'] = (empty($toName)) ? (string) $to[1] : $toName;
		$email['from_email'] = (empty($fromEmail)) ? (string) $from[0] : $fromEmail;
		$email['from_name'] = (empty($fromName)) ? (string) $from[1] : $fromName;
		$email['reply_to_email'] = (empty($replyToEmail)) ? (string) $replyTo[0] : $replyToEmail;
		$email['reply_to_name'] = (empty($replyToName)) ? (string) $replyTo[1] : $replyToName;

		// validate
		if(!empty($email['to_email']) && !SpoonFilter::isEmail($email['to_email'])) throw new BackendMailerException('Invalid e-mail address for recipient.');
		if(!empty($email['from_email']) && !SpoonFilter::isEmail($email['from_email'])) throw new BackendMailerException('Invalid e-mail address for sender.');
		if(!empty($email['reply_to_email']) && !SpoonFilter::isEmail($email['reply_to_email'])) throw new BackendMailerException('Invalid e-mail address for reply-to address.');

		// build array
		$email['subject'] = SpoonFilter::htmlentitiesDecode($subject);
		$email['html'] = self::getTemplateContent($template, $variables);
		if($queue) $email['send_on'] = BackendModel::getUTCDate('Y-m-d H') .'00:00';

		// get db
		$db = BackendModel::getDB();

		// insert the email into the database
		$id = $db->insert('emails', $email);

		// if queue was not enabled, send this mail right away
		if(!$queue) self::send($id);
	}


	/**
	 * Returns the content from a given template
	 *
	 * @return	string
	 * @param	string	$template
	 * @param	array[optional]	$variables
	 */
	private static function getTemplateContent($template, $variables = null)
	{
		// new template instance
		$tpl = new SpoonTemplate();
		$tpl->setCompileDirectory(BACKEND_CACHE_PATH .'/templates');
		$tpl->setForceCompile(true);

		// variables were set
		if(!empty($variables)) $tpl->assign($variables);

		// @todo	parse locale...

		// grab the content
		$content = $tpl->getContent($template);

		// replace internal links/images
		$search = array('href="/', 'src="/');
		$replace = array('href="'. SITE_URL .'/', 'src="'. SITE_URL .'/');
		$content = str_replace($search, $replace, $content);

		// return the content
		return (string) $content;
	}


	/**
	 * Send an email
	 *
	 * @return	void
	 * @param	int $id
	 */
	public static function send($id)
	{
		// redefine
		$id = (int) $id;

		// get db
		$db = BackendModel::getDB();

		// get record
		$emailRecord = (array) $db->getRecord('SELECT *
												FROM emails AS e
												WHERE e.id = ?;',
												array($id));

		// get settings
		$SMTPServer = BackendModel::getSetting('core', 'smtp_server');
		$SMTPPort = BackendModel::getSetting('core', 'smtp_port', 25);
		$SMTPUsername = BackendModel::getSetting('core', 'smtp_username');
		$SMTPPassword = BackendModel::getSetting('core', 'smtp_password');

		// create new SpoonEmail-instance
		$email = new SpoonEmail();
		$email->setTemplateCompileDirectory(BACKEND_CACHE_PATH .'/templates');

		// set authentication if needed
		if($SMTPUsername !== null && $SMTPPassword !== null)
		{
			// set server and connect with SMTP
			$email->setSMTPConnection($SMTPServer, $SMTPPort, 10);
			$email->setSMTPAuth($SMTPUsername, $SMTPPassword);
		}

		// set some properties
		$email->setFrom($emailRecord['from_email'], $emailRecord['from_name']);
		$email->addRecipient($emailRecord['to_email'], $emailRecord['to_name']);
		$email->setReplyTo($emailRecord['reply_to_email']);

		$email->setSubject($emailRecord['subject']);
		$email->setHTMLContent($emailRecord['html']);

		// send the email
		if($email->send())
		{
			// remove the email
			$db->delete('emails', 'id = ?', array($id));
		}
	}

}


/**
 * BackendMailer
 *
 * This class is used when an exceptions occures in the BackendMailer class
 *
 * @package		backend
 * @subpackage	mailer
 *
 * @author		Davy Hellemans <davy@netlash.com>
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendMailerException extends BackendException {}

?>