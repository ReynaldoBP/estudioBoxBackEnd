<?php

namespace App\Entity;

use App\Repository\InfoRespuestaDeficientesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * InfoRespuestaDeficientes
 * @ORM\Table(name="INFO_RESPUESTA_DEFICIENTES")
 * @ORM\Entity(repositoryClass=InfoRespuestaDeficientesRepository::class)
 */
class InfoRespuestaDeficientes
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="ID_RESPUESTA_DEFICIENTE",type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=200)
     */
    private $DESCRIPCION;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $RESPUESTA;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $ESTADO;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $USR_CREACION;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $FE_CREACION;

    /**
    * @var InfoEmpresa
    *
    * @ORM\ManyToOne(targetEntity="InfoEmpresa")
    * @ORM\JoinColumns({
    * @ORM\JoinColumn(name="EMPRESA_ID", referencedColumnName="ID_EMPRESA")
    * })
    */
    private $EMPRESA_ID;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDESCRIPCION(): ?string
    {
        return $this->DESCRIPCION;
    }

    public function setDESCRIPCION(string $DESCRIPCION): self
    {
        $this->DESCRIPCION = $DESCRIPCION;

        return $this;
    }

    public function getRESPUESTA(): ?string
    {
        return $this->RESPUESTA;
    }

    public function setRESPUESTA(string $RESPUESTA): self
    {
        $this->RESPUESTA = $RESPUESTA;

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

    public function setUSRCREACION(?string $USR_CREACION): self
    {
        $this->USR_CREACION = $USR_CREACION;

        return $this;
    }

    public function getFECREACION(): ?\DateTimeInterface
    {
        return $this->FE_CREACION;
    }

    public function setFECREACION(?\DateTimeInterface $FE_CREACION): self
    {
        $this->FE_CREACION = $FE_CREACION;

        return $this;
    }

    public function getEMPRESAID(): ?int
    {
        return $this->EMPRESA_ID;
    }

    public function setEMPRESAID(int $EMPRESA_ID): self
    {
        $this->EMPRESA_ID = $EMPRESA_ID;

        return $this;
    }
}
