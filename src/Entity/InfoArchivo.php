<?php

namespace App\Entity;

use App\Repository\InfoArchivoRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * InfoArchivo
 * @ORM\Table(name="INFO_ARCHIVO")
 * @ORM\Entity(repositoryClass=InfoArchivoRepository::class)
 */
class InfoArchivo
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="ID_ARCHIVO",type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $NOMBRE;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $TIPO;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $TAMAÑO;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $USR_CREACION;

    /**
     * @ORM\Column(type="datetime")
     */
    private $FE_CREACION;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $UBICACION;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNOMBRE(): ?string
    {
        return $this->NOMBRE;
    }

    public function setNOMBRE(string $NOMBRE): self
    {
        $this->NOMBRE = $NOMBRE;

        return $this;
    }

    public function getTIPO(): ?string
    {
        return $this->TIPO;
    }

    public function setTIPO(string $TIPO): self
    {
        $this->TIPO = $TIPO;

        return $this;
    }

    public function getTAMAÑO(): ?string
    {
        return $this->TAMAÑO;
    }

    public function setTAMAÑO(string $TAMAÑO): self
    {
        $this->TAMAÑO = $TAMAÑO;

        return $this;
    }

    public function getUSRCREACION(): ?string
    {
        return $this->USR_CREACION;
    }

    public function setUSRCREACION(string $USR_CREACION): self
    {
        $this->USR_CREACION = $USR_CREACION;

        return $this;
    }

    public function getFECREACION(): ?\DateTimeInterface
    {
        return $this->FE_CREACION;
    }

    public function setFECREACION(\DateTimeInterface $FE_CREACION): self
    {
        $this->FE_CREACION = $FE_CREACION;

        return $this;
    }

    public function getUBICACION(): ?string
    {
        return $this->UBICACION;
    }

    public function setUBICACION(string $UBICACION): self
    {
        $this->UBICACION = $UBICACION;

        return $this;
    }
}
