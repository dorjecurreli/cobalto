<?php

namespace App\Service;

use App\Entity\Artist;
use App\Entity\Artwork;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;

class ArtworkImporter
{
    private EntityManagerInterface $em;
    private FilesystemOperator $storage;

    public function __construct(EntityManagerInterface $em, FilesystemOperator $assetsStorage)
    {
        $this->em = $em;
        $this->storage = $assetsStorage;
    }

    /**
     * @throws FilesystemException
     */
    public function importArtworks(string $bucketPath = '.'): void
    {
        $files = $this->storage->listContents($bucketPath, false);

        foreach ($files as $file) {
            if ($file['type'] !== 'file') {
                continue;
            }

            $pattern = '/^[a-zA-Z0-9_]+_[a-zA-Z0-9_]+_[a-f0-9]{12}\.jpg$/';
            if (!preg_match($pattern, $file['path'])) {
                echo 'filename not valid ' . $file['path'] . "\n";
                continue;
            }

            $fileName = $file['path'];
            $parts = explode('_', pathinfo($fileName, PATHINFO_FILENAME));

            $artistName = ucfirst($parts[0]) . ' ' . ucfirst($parts[1]);

            $artist = $this->em->getRepository(Artist::class)->findOneBy(['name' => $artistName]);

            if (!$artist) {
                $artist = new Artist();
                $artist->setName($artistName);
                $this->em->persist($artist);
                $this->em->flush();
            }

            $artwork = new Artwork();
            $artwork->setImageName($file['path']);
            $artwork->setArtist($artist);

            $this->em->persist($artwork);
        }

        $this->em->flush();
    }
}
