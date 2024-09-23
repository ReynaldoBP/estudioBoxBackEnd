<?php

namespace App\Entity;

use App\Repository\InfoDetalleBitacoraRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * InfoDetalleBitacora
 * @ORM\Table(name="INFO_DETALLE_BITACORA")
 * @ORM\Entity(repositoryClass="App\Repository\InfoDetalleBitacoraRepository")
 */
class InfoDetalleBitacora
{
    /**
     * @ORM\Column(name="ID_DETALLE_BITACORA", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
    * @var InfoBitacora
    *
    * @ORM\ManyToOne(targetEntity="InfoBitacora")
    * @ORM\JoinColumns({
    * @ORM\JoinColumn(name="BITACORA_ID", referencedColumnName="ID_BITACORA")
    * })
    */
    private $BITACORA_ID;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $CAMPO;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $VALOR_ANTERIOR;

    /**
    * @var InfoUsuario
    *
    * @ORM\ManyToOne(targetEntity="InfoUsuario")
    * @ORM\JoinColumns({
    * @ORM\JoinColumn(name="USUARIO_ID", referencedColumnName="ID_USUARIO")
    * })
    */
    private $USUARIO_ID;

    /**
     * @ORM\Column(type="string")
     */
    private $FE_CREACION;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $VALOR_ACTUAL;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set BITACORAID
     *
     * @param \App\Entity\InfoBitacora $BITACORAID
     *
     * @return InfoBitacora
     */
    public function setBITACORAID(\App\Entity\InfoBitacora $BITACORAID = null)
    {
        $this->BITACORA_ID = $BITACORAID;

        return $this;
    }

    /**
     * Get BITACORAID
     *
     * @return \App\Entity\InfoBitacora
     */
    public function getBITACORAID()
    {
        return $this->BITACORA_ID;
    }

    public function getCAMPO(): ?string
    {
        return $this->CAMPO;
    }

    public function setCAMPO(?string $CAMPO): self
    {
        $this->CAMPO = $CAMPO;

        return $this;
    }

    public function getVALORANTERIOR(): ?string
    {
        return $this->VALOR_ANTERIOR;
    }

    public function setVALORANTERIOR(?string $VALOR_ANTERIOR): self
    {
        $this->VALOR_ANTERIOR = $VALOR_ANTERIOR;

        return $this;
    }

    public function getVALORACTUAL(): ?string
    {
        return $this->VALOR_ACTUAL;
    }

    public function setVALORACTUAL(?string $VALOR_ACTUAL): self
    {
        $this->VALOR_ACTUAL = $VALOR_ACTUAL;

        return $this;
    }

    /**
     * Set USUARIOID
     *
     * @param \App\Entity\InfoUsuario $USUARIOID
     *
     * @return InfoDetalleBitacora
     */
    public function setUSUARIOID(\App\Entity\InfoUsuario $USUARIOID = null)
    {
        $this->USUARIO_ID = $USUARIOID;

        return $this;
    }

    /**
     * Get USUARIOID
     *
     * @return \App\Entity\InfoUsuario
     */
    public function getUSUARIOID()
    {
        return $this->USUARIO_ID;
    }

    public function getFECREACION(): ?string
    {
        return $this->FE_CREACION;
    }

    public function setFECREACION(string $FE_CREACION): self
    {
        $this->FE_CREACION = $FE_CREACION;

        return $this;
    }

}
