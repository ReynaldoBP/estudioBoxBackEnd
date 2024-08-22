<?php

namespace App\Entity;

use App\Repository\InfoOpcionRespuestaRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InfoOpcionRespuestaRepository::class)
 */
/**
 * InfoOpcionRespuesta
 * @ORM\Table(name="INFO_OPCION_RESPUESTA")
 * @ORM\Entity(repositoryClass="App\Repository\InfoOpcionRespuestaRepository")
 */
class InfoOpcionRespuesta
{
    /**
     * @var int
     *
     * @ORM\Column(name="ID_OPCION_RESPUESTA", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
    * @var InfoPregunta
    *
    * @ORM\ManyToOne(targetEntity="InfoPregunta")
    * @ORM\JoinColumns({
    * @ORM\JoinColumn(name="PREGUNTA_ID", referencedColumnName="ID_PREGUNTA")
    * })
    */
    private $PREGUNTA_ID;

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
     * @ORM\Column(type="string", length=4000)
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

    /**
     * Get PREGUNTA_ID
     *
     * @return \App\Entity\InfoPregunta
     */
    public function getPREGUNTAID()
    {
        return $this->PREGUNTA_ID;
    }

    /**
     * Set setPREGUNTAID
     *
     * @param \App\Entity\InfoPregunta $PREGUNTA_ID
     *
     * @return InfoOpcionRespuestaRepository
     */
    public function setPREGUNTAID(\App\Entity\InfoPregunta $PREGUNTA_ID = null)
    {
        $this->PREGUNTA_ID = $PREGUNTA_ID;

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
     * @return InfoOpcionRespuestaRepository
     */
    public function setTIPOOPCIONRESPUESTAID(\App\Entity\AdmiTipoOpcionRespuesta $TIPO_OPCION_RESPUESTA_ID = null)
    {
        $this->TIPO_OPCION_RESPUESTA_ID = $TIPO_OPCION_RESPUESTA_ID;

        return $this;
    }

    public function getVALOR(): ?string
    {
        return $this->VALOR;
    }

    public function setVALOR(string $VALOR): self
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
