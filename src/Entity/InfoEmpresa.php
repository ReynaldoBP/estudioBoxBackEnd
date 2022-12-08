<?php

namespace App\Entity;

use App\Repository\InfoEmpresaRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * InfoEmpresa
 * @ORM\Table(name="INFO_EMPRESA")
 * @ORM\Entity(repositoryClass="App\Repository\InfoEmpresaRepository")
 */
class InfoEmpresa
{
    /**
     * @var int
     *
     * @ORM\Column(name="ID_EMPRESA", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $TIPO_IDENTIFICACION;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $IDENTIFICACION;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $REPRESENTANTE_LEGAL;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $RAZON_SOCIAL;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $NOMBRE_COMERCIAL;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $DIRECCION_TRIBUTARIO;

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
     * @var string
     *
     * @ORM\Column(name="USR_MODIFICACION", type="string", length=255, nullable=true)
     */
    private $USR_MODIFICACION;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="FE_MODIFICACION", type="datetime", nullable=true)
     */
    private $FE_MODIFICACION;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTIPOIDENTIFICACION(): ?string
    {
        return $this->TIPO_IDENTIFICACION;
    }

    public function setTIPOIDENTIFICACION(string $TIPO_IDENTIFICACION): self
    {
        $this->TIPO_IDENTIFICACION = $TIPO_IDENTIFICACION;

        return $this;
    }

    public function getIDENTIFICACION(): ?string
    {
        return $this->IDENTIFICACION;
    }

    public function setIDENTIFICACION(string $IDENTIFICACION): self
    {
        $this->IDENTIFICACION = $IDENTIFICACION;

        return $this;
    }

    public function getREPRESENTANTELEGAL(): ?string
    {
        return $this->REPRESENTANTE_LEGAL;
    }

    public function setREPRESENTANTELEGAL(string $REPRESENTANTE_LEGAL): self
    {
        $this->REPRESENTANTE_LEGAL = $REPRESENTANTE_LEGAL;

        return $this;
    }

    public function getRAZONSOCIAL(): ?string
    {
        return $this->RAZON_SOCIAL;
    }

    public function setRAZONSOCIAL(string $RAZON_SOCIAL): self
    {
        $this->RAZON_SOCIAL = $RAZON_SOCIAL;

        return $this;
    }

    public function getNOMBRECOMERCIAL(): ?string
    {
        return $this->NOMBRE_COMERCIAL;
    }

    public function setNOMBRECOMERCIAL(string $NOMBRE_COMERCIAL): self
    {
        $this->NOMBRE_COMERCIAL = $NOMBRE_COMERCIAL;

        return $this;
    }

    public function getDIRECCIONTRIBUTARIO(): ?string
    {
        return $this->DIRECCION_TRIBUTARIO;
    }

    public function setDIRECCIONTRIBUTARIO(string $DIRECCION_TRIBUTARIO): self
    {
        $this->DIRECCION_TRIBUTARIO = $DIRECCION_TRIBUTARIO;

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
