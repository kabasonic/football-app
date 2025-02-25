<?php

namespace App\Application\Dto\Request\Player;

use Symfony\Component\Validator\Constraints as Assert;

class UpdatePlayerRequest
{
    public function __construct(

        #[Assert\Type('string')]
        #[Assert\NotBlank(message: "First name can't be empty")]
        #[Assert\Length(
            min: 3,
            max: 100,
            minMessage: "First name can't be less than 255 characters.",
            maxMessage: "First name can't be less than 255 characters."
        )]
        #[Assert\NotNull(message: "First name can't be null")]
        public string $firstName,

        #[Assert\Type('string')]
        #[Assert\NotBlank(message: "Last name can't be empty")]
        #[Assert\Length(
            min: 3,
            max: 100,
            minMessage: "Last name can't be less than 255 characters.",
            maxMessage: "Last name can't be less than 255 characters."
        )]
        #[Assert\NotNull(message: "Last name can't be null")]
        public string $lastName,

        #[Assert\Type('integer')]
        #[Assert\NotBlank(message: "Age can't be empty")]
        #[Assert\NotNull(message: "Age can't be null")]
        #[Assert\Range(notInRangeMessage: "Range age is 3 - 100 years.", min: 3, max: 100)]
        public int $age,

        #[Assert\Type('string')]
        #[Assert\NotBlank(message: "Position can't be empty")]
        #[Assert\Length(
            min: 3,
            max: 150,
            minMessage: "Position can't be less than 255 characters.",
            maxMessage: "Position can't be less than 255 characters."
        )]
        #[Assert\NotNull(message: "Position can't be null")]
        public string $position,
    )
    {
    }
}
