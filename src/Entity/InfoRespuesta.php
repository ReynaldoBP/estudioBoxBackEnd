<?php

namespace App\Entity;

use App\Repository\InfoRespuestaRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * InfoClienteEncuesta
 * @ORM\Table(name="INFO_RESPUESTA")
 * @ORM\Entity(repositoryClass=InfoRespuestaRepository::class)
 */
class InfoRespuesta
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="ID_RESPUESTA",type="integer")
     */
    private $id;

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
    * @var InfoCliente
    *
    * @ORM\ManyToOne(targetEntity="InfoCliente")
    * @ORM\JoinColumns({
    * @ORM\JoinColumn(name="CLIENTE_ID", referencedColumnName="ID_CLIENTE")
    * })
    */
    private $CLIENTE_ID;

    /**
    * @var InfoClienteEncuesta
    *
    * @ORM\ManyToOne(targetEntity="InfoClienteEncuesta")
    * @ORM\JoinColumns({
    * @ORM\JoinColumn(name="CLT_ENCUESTA_ID", referencedColumnName="ID_CLT_ENCUESTA")
    * })
    */
    private $CLT_ENCUESTA_ID;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $RESPUESTA;

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
     * @return InfoRespuesta
     */
    public function setPREGUNTAID(\App\Entity\InfoPregunta $PREGUNTA_ID = null)
    {
        $this->PREGUNTA_ID = $PREGUNTA_ID;

        return $this;
    }

    /**
     * Get getCLIENTEID
     *
     * @return \App\Entity\InfoCliente
     */
    public function getCLIENTEID()
    {
        return $this->CLIENTE_ID;
    }

    /**
     * Set setCLIENTEID
     *
     * @param \App\Entity\InfoCliente $CLIENTE_ID
     *
     * @return InfoRespuesta
     */
    public function setCLIENTEID(\App\Entity\InfoCliente $CLIENTE_ID = null)
    {
        $this->CLIENTE_ID = $CLIENTE_ID;

        return $this;
    }

    /**
     * Get getCLIENTEID
     *
     * @return \App\Entity\InfoClienteEncuesta
     */
    public function getCLTENCUESTAID()
    {
        return $this->CLT_ENCUESTA_ID;
    }

    /**
     * Set setCLIENTEID
     *
     * @param \App\Entity\InfoClienteEncuesta $CLT_ENCUESTA_ID
     *
     * @return InfoRespuesta
     */
    public function setCLTENCUESTAID(\App\Entity\InfoClienteEncuesta $CLT_ENCUESTA_ID = null)
    {
        $this->CLT_ENCUESTA_ID = $CLT_ENCUESTA_ID;

        return $this;
    }

    public function getRESPUESTA(): ?string
    {
        return $this->RESPUESTA;
    }

    public function setRESPUESTA(?string $RESPUESTA): self
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
