<?php


namespace Codelines\LaravelMediaController;

use App\Modules\Media\Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * Trait HasMedia
 * @package App\Modules\Media\Traits
 */
trait HasMedia
{
    /**
     * @return string
     */
    public function directory(): string
    {
        return "files";
    }

    /**
     * @return bool
     */
    public function storageDriver(): string
    {
        return 'public';
    }

    /**
     * @return bool
     */
    public function randomizedName(): bool
    {
        return true;
    }

    /**
     * @param $files
     * @param string $album
     * @return bool
     */
    public function saveMedia($files, $album = "default"): bool
    {
        $storage = $this->storageDriver();
        $directory = $this->directory();

        if($files){
            if(is_array($files)){
                foreach ($files as $file){
                    $path = $file->store($directory, $storage);
                    $name = $file->getClientOriginalName();
                    $mime = $file->getClientMimeType();

                    $this->saveToDB($path, $this, $name, $album, $mime);
                }
            }else{
                $path = $files->store($directory, $storage);
                $name = $files->getClientOriginalName();
                $mime = $files->getClientMimeType();
                $this->saveToDB($path, $this, $name, $album, $mime);
            }
        }
        return false;
    }

    public function deleteMedia(array $mediaIds)
    {
        $itemMedia = $this->media()->whereIn('id', $mediaIds)->get();
        foreach($itemMedia as $media){
            Storage::disk($media->fs)->delete($media->path);
            $media->delete();
        }
    }

    public function clearMedia()
    {
        $itemMedia = $this->media;
        foreach($itemMedia as $media){
            Storage::disk($media->fs)->delete($media->path);
            $media->delete();
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function media(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Media::class, 'object');
    }

    public function album($album="default"): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->media()->where('album', $album);
    }

    /**
     * @param string $path
     * @param Model $model
     * @param string $orig_name
     * @param string $album
     * @param string $mime
     */
    protected function saveToDB(string $path, Model $model, string $orig_name, string $album, string $mime)
    {
        $media = new Media();
        $media->path = $path;
        $media->object_type = get_class($model);
        $media->object_id = $model->getKey();
        $media->orig_name = $orig_name;
        $media->fs = $this->storageDriver();
        $media->album = $album;
        $media->mime_type = $mime;
        $media->save();
    }
}
