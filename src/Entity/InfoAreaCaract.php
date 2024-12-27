<?php

 namespace App\Entity;

 use App\Repository\InfoAreaCaractRepository;
 use Doctrine\ORM\Mapping as ORM;
 
 /**
  * InfoAreaCaract
  * @ORM\Table(name="INFO_AREA_CARACT")
  * @ORM\Entity(repositoryClass="App\Repository\InfoAreaCaractRepository")
  */
class InfoAreaCaract
{
    /**
     * @var int
     *
     * @ORM\Column(name="ID_AREA_CARACT", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
    * @var InfoArea
    *
    * @ORM\ManyToOne(targetEntity="InfoArea")
    * @ORM\JoinColumns({
    * @ORM\JoinColumn(name="AREA_ID", referencedColumnName="ID_AREA")
    * })
    */
    private $AREA_ID;

    /**
    * @var AdmiCaracteristica
    *
    * @ORM\ManyToOne(targetEntity="AdmiCaracteristica")
    * @ORM\JoinColumns({
    * @ORM\JoinColumn(name="CARACTERISTICA_ID", referencedColumnName="ID_CARACTERISTICA")
    * })
    */
    private $CARACTERISTICA_ID;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $VALOR1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $VALOR2;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $VALOR3;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $ESTADO;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $USR_CREACION;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $FE_CREACION;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get AREA_ID
     *
     * @return \App\Entity\InfoArea
     */
    public function getAREAID()
    {
        return $this->AREA_ID;
    }

    /**
     * Set setAREAID
     *
     * @param \App\Entity\InfoArea $AREA_ID
     *
     * @return InfoArea
     */
    public function setAREAID(\App\Entity\InfoArea $AREA_ID = null)
    {
        $this->AREA_ID = $AREA_ID;

        return $this;
    }

    /**
     * Get CARACTERISTICA_ID
     *
     * @return \App\Entity\AdmiCaracteristica
     */
    public function getCARACTERISTICAID()
    {
        return $this->CARACTERISTICA_ID;
    }

    public function setCARACTERISTICAID(\App\Entity\AdmiCaracteristica $CARACTERISTICA_ID = null)
    {
        $this->CARACTERISTICA_ID = $CARACTERISTICA_ID;

        return $this;
    }

    public function getVALOR1(): ?string
    {
        return $this->VALOR1;
    }

    public function setVALOR1(?string $VALOR1): self
    {
        $this->VALOR1 = $VALOR1;

        return $this;
    }

    public function getVALOR2(): ?string
    {
        return $this->VALOR2;
    }

    public function setVALOR2(?string $VALOR2): self
    {
        $this->VALOR2 = $VALOR2;

        return $this;
    }

    public function getVALOR3(): ?string
    {
        return $this->VALOR3;
    }

    public function setVALOR3(?string $VALOR3): self
    {
        $this->VALOR3 = $VALOR3;

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
}
