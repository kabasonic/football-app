<?php

namespace App\Application\Dto\Request\Team;

use App\Application\Validator\CurrentYearConstraint;
use Symfony\Component\Validator\Constraints as Assert;

class TeamPayloadRequest
{
    public function __construct(

        #[Assert\Type('string')]
        #[Assert\NotBlank(message: "Name can't be empty")]
        #[Assert\Length(
            min: 3,
            max: 255,
            minMessage: "Name can't be less than 255 characters.",
            maxMessage: "Name can't be less than 255 characters."
        )]
        #[Assert\NotNull(message: "Name can't be null")]
        #[Assert\Regex(
            pattern: '/^[a-zA-Z\s]+$/',
            message: 'Name should contain only alphabetic characters.'
        )]
        public string $name,

        #[Assert\Type('string')]
        #[Assert\NotBlank(message: "City name can't be empty")]
        #[Assert\Length(
            min: 3,
            max: 255,
            minMessage: "City name can't be less than 255 characters.",
            maxMessage: "City name can't be less than 255 characters."
        )]
        #[Assert\NotNull(message: "City name can't be null")]
        #[Assert\Regex(
            pattern: '/^[a-zA-Z\s]+$/',
            message: 'City should contain only alphabetic characters.'
        )]
        public string $city,

        #[Assert\Type('integer')]
        #[Assert\NotBlank(message: "Year of found can't be empty")]
        #[Assert\NotNull(message: "Year of found can't be null")]
        #[CurrentYearConstraint]
        #[Assert\Regex(
            pattern: '/^\d+$/',
            message: 'Year founded should contain only digits.'
        )]
        public int $yearFounded,

        #[Assert\Type('string')]
        #[Assert\NotBlank(message: "Stadium name can't be empty")]
        #[Assert\Length(
            min: 3,
            max: 255,
            minMessage: "Stadium name can't be less than 255 characters.",
            maxMessage: "Stadium name can't be less than 255 characters."
        )]
        #[Assert\NotNull(message: "Stadium name can't be null")]
        #[Assert\Regex(
            pattern: '/^[a-zA-Z\s]+$/',
            message: 'Stadium name should contain only alphabetic characters.'
        )]
        public string $stadiumName,
    )
    {
    }
}
