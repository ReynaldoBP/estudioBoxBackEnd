<?php

namespace App\Entity;

use App\Repository\AdmiTipoOpcionRespuestaRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * AdmiTipoOpcionRespuesta
 *
 * @ORM\Table(name="ADMI_TIPO_OPCION_RESPUESTA")
 * @ORM\Entity(repositoryClass=AdmiTipoOpcionRespuestaRepository::class)
 */
class AdmiTipoOpcionRespuesta
{
    /**
     * @ORM\Column(name="ID_TIPO_OPCION_RESPUESTA", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $TIPO_RESPUESTA;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $DESCRIPCION;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $VALOR;

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

    public function getTIPO_RESPUESTA(): ?string
    {
        return $this->TIPO_RESPUESTA;
    }

    public function setTIPO_RESPUESTA(?string $TIPO_RESPUESTA): self
    {
        $this->TIPO_RESPUESTA = $TIPO_RESPUESTA;

        return $this;
    }

    public function getDESCRIPCION(): ?string
    {
        return $this->DESCRIPCION;
    }

    public function setDESCRIPCION(?string $DESCRIPCION): self
    {
        $this->DESCRIPCION = $DESCRIPCION;

        return $this;
    }

    public function getVALOR(): ?int
    {
        return $this->VALOR;
    }

    public function setVALOR(?int $VALOR): self
    {
        $this->VALOR = $VALOR;

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
