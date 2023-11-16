<?php

namespace App\Entity;

use App\Repository\InfoPublicidadArchivoRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * InfoPublicidadArchivo
 * @ORM\Table(name="INFO_PUBLICIDAD_ARCHIVO")
 * @ORM\Entity(repositoryClass=InfoPublicidadArchivoRepository::class)
 */
class InfoPublicidadArchivo
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="ID_RELACION",type="integer")
     */
    private $id;

    /**
    * @var InfoPublicidad
    *
    * @ORM\ManyToOne(targetEntity="InfoPublicidad")
    * @ORM\JoinColumns({
    * @ORM\JoinColumn(name="PUBLICIDAD_ID", referencedColumnName="ID_PUBLICIDAD")
    * })
    */
    private $publicidad;

    /**
    * @var InfoArchivo
    *
    * @ORM\ManyToOne(targetEntity="InfoArchivo")
    * @ORM\JoinColumns({
    * @ORM\JoinColumn(name="ARCHIVO_ID", referencedColumnName="ID_ARCHIVO")
    * })
    */
    private $archivo;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPUBLICIDAD(): ?InfoPublicidad
    {
        return $this->publicidad;
    }

    public function setPUBLICIDAD(?InfoPublicidad $publicidad): self
    {
        $this->publicidad = $publicidad;

        return $this;
    }

    public function getARCHIVO(): ?InfoArchivo
    {
        return $this->archivo;
    }

    public function setARCHIVO(?InfoArchivo $archivo): self
    {
        $this->archivo = $archivo;

        return $this;
    }
}
