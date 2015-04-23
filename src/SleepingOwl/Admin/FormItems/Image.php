<?php namespace SleepingOwl\Admin\FormItems;

use Input;
use Response;
use Route;
use SleepingOwl\Admin\AssetManager\AssetManager;
use SleepingOwl\Admin\Interfaces\WithRoutesInterface;
use Validator;

class Image extends NamedFormItem implements WithRoutesInterface
{

	protected $view = 'image';

	public function initialize()
	{
		parent::initialize();

		AssetManager::addScript('admin::default/js/formitems/image/flow.min.js');
		AssetManager::addScript('admin::default/js/formitems/image/init.js');
	}

	public static function registerRoutes()
	{
		Route::post('formitems/image/upload', [
			'as' => 'admin.formitems.image.upload',
			function ()
			{
				$validator = Validator::make(Input::all(), [
					'file' => 'image',
				]);
				if ($validator->fails())
				{
					return Response::make($validator->errors()->get('file'), 400);
				}
				$file = Input::file('file');
				$filename = md5(time() . $file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension();
				$path = config('admin.imagesUploadDirectory');
				$fullpath = public_path($path);
				$file->move($fullpath, $filename);
				$value = $path . '/' . $filename;
				return [
					'url'   => asset($value),
					'value' => $value,
				];
			}
		]);
	}
}