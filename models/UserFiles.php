<?php


namespace app\models;


use yii\base\Model;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

/**
 *
 * @property UploadedFile $yearFile
 * @property UploadedFile $monthFile
 * @property UploadedFile $deficitFile
 */
class UserFiles extends Model
{
    const USERS_FILES = 'files';
    const FILENAMES = [
        'Отчет за год' => 'yearFile',
        'Отчет за месяц' => 'monthFile',
        'Дефицит' => 'deficitFile',
        'Себестоимость' => 'costFile'
    ];

    public $yearFile = '';
    public $monthFile = '';
    public $deficitFile = '';
    public $costFile = '';

    public function upload(UploadedFile $uploadedFile, $fileName) {
        if (!$uploadedFile) {
            return false;
        }
        if($this->validate()){
            $path = \Yii::getAlias('@webroot/').self::USERS_FILES.'/'.\Yii::$app->user->id;
            FileHelper::createDirectory($path, 0755);
            $newFileName=$path.'/'.$fileName.'.'.$uploadedFile->extension;
            if(!$uploadedFile->saveAs($newFileName)){
                $this->addError('file','Не удалось сохранить файл');
                return false;
            }
        }

        return false;
    }

    public static function clearUserFiles($userId) {
        $path = \Yii::getAlias('@webroot/').self::USERS_FILES.'/'.$userId;
        FileHelper::removeDirectory($path);
    }
}