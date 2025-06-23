<?php

namespace STS\ZipStream\Models;

use GuzzleHttp\Psr7\Utils;
use Illuminate\Filesystem\FilesystemAdapter;
use League\Flysystem\FilesystemException;
use Psr\Http\Message\StreamInterface;
use STS\ZipStream\Exceptions\DiskNotSpecified;
use STS\ZipStream\Exceptions\NotWritableException;
use STS\ZipStream\OutputStream;

class FtpFile extends File
{
    protected FilesystemAdapter $disk;

    public function setDisk(FilesystemAdapter $disk = null): static
    {
        $this->disk = $disk;
        return $this;
    }

    /**
     * @throws DiskNotSpecified
     */
    public function getDisk(): FilesystemAdapter
    {
        if (!isset($this->disk)) {
            throw new DiskNotSpecified('FTP disk not specified.');
        }

        return $this->disk;
    }

    /**
     * @throws FilesystemException|DiskNotSpecified
     */
    public function calculateFilesize(): int
    {
        return $this->getDisk()->fileSize($this->getSource());
    }

    /**
     * @throws DiskNotSpecified
     */
    protected function buildReadableStream(): StreamInterface
    {
        return Utils::streamFor(
            $this->getDisk()->readStream($this->getSource())
        );
    }

    /**
     * @throws NotWritableException
     */
    protected function buildWritableStream(): OutputStream
    {
        throw new NotWritableException();
    }

    public function canPredictZipDataSize(): bool
    {
        return true;
    }
}
