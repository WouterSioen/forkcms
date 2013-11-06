<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * This is the upload-action, it will upload send images
 *
 * @author Wouter sioen <wouter.sioen@wijs.be>
 */
class BackendCoreUpload extends BackendBaseAction
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		// fetch the uploaded file from the request
		$request = Request::createFromGlobals();
		$attachment = $request->files->get('attachment');
		$uploadedFile = $attachment['file'];

		// create the upload folder if necessary
		$path = FRONTEND_FILES_PATH . '/sir-trevor/images';
		$url = FRONTEND_FILES_URL . '/sir-trevor/images';
		$fs = new Filesystem();
		if(!$fs->exists($path)) $fs->mkdir($path);

		// create an image name
		$pathInfo = pathinfo($uploadedFile->getClientOriginalName());
		$fileName = $pathInfo['filename'];
		$extension = $pathInfo['extension'];

		while($fs->exists($path . '/' . $fileName . '.' . $extension))
		{
			$fileName = BackendModel::addNumber($fileName);
		}

		// upload the image
		$uploadedFile->move($path, $fileName . '.' . $extension);

		// return an empty response. Everything went fine
		$response = new Response(
			json_encode(array('file' => array(
				'url' => $url . '/' . $fileName . '.' . $extension,
				'filename' => $fileName . '.' . $extension
			))),
			200,
			array('Content-Type' => 'application/json')
		);
		$response->send();
		exit;
	}
}
