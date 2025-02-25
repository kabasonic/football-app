<?php

namespace App\Application\Dto\Request\Team;

use Symfony\Component\Validator\Constraints as Assert;

class RelocateTeamRequest
{
    public function __construct(

        #[Assert\Type('string')]
        #[Assert\NotBlank(message: "City name can't be empty")]
        #[Assert\Length(
            min: 3,
            max: 255,
            minMessage: "City name can't be less than 255 characters.",
            maxMessage: "City name can't be less than 255 characters."
        )]
        #[Assert\NotNull(message: "City name can't be null")]
        public string $city,
    )
    {
    }
}
