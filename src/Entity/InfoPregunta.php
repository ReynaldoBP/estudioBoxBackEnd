<?php

namespace App\Entity;

use App\Repository\InfoPreguntaRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * InfoPregunta
 * @ORM\Table(name="INFO_PREGUNTA")
 * @ORM\Entity(repositoryClass=InfoPreguntaRepository::class)
 */
class InfoPregunta
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="ID_PREGUNTA",type="integer")
     */
    private $id;

    /**
    * @var InfoEncuesta
    *
    * @ORM\ManyToOne(targetEntity="InfoEncuesta")
    * @ORM\JoinColumns({
    * @ORM\JoinColumn(name="ENCUESTA_ID", referencedColumnName="ID_ENCUESTA")
    * })
    */
    private $ENCUESTA_ID;

    /**
    * @var AdmiTipoOpcionRespuesta
    *
    * @ORM\ManyToOne(targetEntity="AdmiTipoOpcionRespuesta")
    * @ORM\JoinColumns({
    * @ORM\JoinColumn(name="TIPO_OPCION_RESPUESTA_ID", referencedColumnName="ID_TIPO_OPCION_RESPUESTA")
    * })
    */
    private $TIPO_OPCION_RESPUESTA_ID;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $DESCRIPCION;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $OBLIGATORIA;

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
    /**
     * Get ENCUESTA_ID
     *
     * @return \App\Entity\InfoEncuesta
     */
    public function getENCUESTAID()
    {
        return $this->ENCUESTA_ID;
    }

    /**
     * Set setENCUESTAID
     *
     * @param \App\Entity\InfoEncuesta $ENCUESTA_ID
     *
     * @return InfoPregunta
     */
    public function setENCUESTAID(\App\Entity\InfoEncuesta $ENCUESTA_ID = null)
    {
        $this->ENCUESTA_ID = $ENCUESTA_ID;

        return $this;
    }

    /**
     * Get TIPO_OPCION_RESPUESTA_ID
     *
     * @return \App\Entity\AdmiTipoOpcionRespuesta
     */
    public function getTIPOOPCIONRESPUESTAID()
    {
        return $this->TIPO_OPCION_RESPUESTA_ID;
    }

    /**
     * Set TIPO_OPCION_RESPUESTA_ID
     *
     * @param \App\Entity\AdmiTipoOpcionRespuesta $TIPO_OPCION_RESPUESTA_ID
     *
     * @return InfoPregunta
     */
    public function setTIPOOPCIONRESPUESTAID(\App\Entity\AdmiTipoOpcionRespuesta $TIPO_OPCION_RESPUESTA_ID = null)
    {
        $this->TIPO_OPCION_RESPUESTA_ID = $TIPO_OPCION_RESPUESTA_ID;

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

    public function getOBLIGATORIA(): ?string
    {
        return $this->OBLIGATORIA;
    }

    public function setOBLIGATORIA(?string $OBLIGATORIA): self
    {
        $this->OBLIGATORIA = $OBLIGATORIA;

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
