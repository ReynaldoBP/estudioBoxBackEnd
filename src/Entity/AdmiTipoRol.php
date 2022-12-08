<?php

namespace App\Entity;

use App\Repository\AdmiTipoRolRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * InfoClienteEncuesta
 * @ORM\Table(name="ADMI_TIPO_ROL")
 * @ORM\Entity(repositoryClass=AdmiTipoRolRepository::class)
 */
class AdmiTipoRol
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="ID_TIPO_ROL",type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $DESCRIPCION_TIPO_ROL;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $ESTADO;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $USR_CREACION;

    /**
     * @ORM\Column(type="datetime")
     */
    private $FE_CREACION;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $USR_MODIFICACION;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $FE_MODIFICACION;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDESCRIPCIONTIPOROL(): ?string
    {
        return $this->DESCRIPCION_TIPO_ROL;
    }

    public function setDESCRIPCIONTIPOROL(string $DESCRIPCION_TIPO_ROL): self
    {
        $this->DESCRIPCION_TIPO_ROL = $DESCRIPCION_TIPO_ROL;

        return $this;
    }


    public function getESTADO(): ?string
    {
        return $this->ESTADO;
    }

    public function setESTADO(string $ESTADO): self
    {
        $this->ESTADO = $ESTADO;

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

    public function getUSRMODIFICACION(): ?string
    {
        return $this->USR_MODIFICACION;
    }

    public function setUSRMODIFICACION(?string $USR_MODIFICACION): self
    {
        $this->USR_MODIFICACION = $USR_MODIFICACION;

        return $this;
    }

    public function getFEMODIFICACION(): ?\DateTimeInterface
    {
        return $this->FE_MODIFICACION;
    }

    public function setFEMODIFICACION(?\DateTimeInterface $FE_MODIFICACION): self
    {
        $this->FE_MODIFICACION = $FE_MODIFICACION;

        return $this;
    }

}
