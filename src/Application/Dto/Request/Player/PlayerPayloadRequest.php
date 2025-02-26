<?php

namespace App\Application\Dto\Request\Player;

use Symfony\Component\Validator\Constraints as Assert;

class PlayerPayloadRequest
{
    private const MINIMUM_AGE = 12;
    private const MAXIMUM_AGE = 50;
    private const VALID_POSITIONS = [
        'GOALKEEPER',
        'DEFENDER',
        'MIDFIELDER',
        'FORWARD',
    ];

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
        #[Assert\Regex(
            pattern: '/^[a-zA-Z\s]+$/',
            message: 'First name should contain only alphabetic characters.'
        )]
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
        #[Assert\Regex(
            pattern: '/^[a-zA-Z\s]+$/',
            message: 'Last name should contain only alphabetic characters.'
        )]
        public string $lastName,

        #[Assert\Type('integer')]
        #[Assert\NotBlank(message: "Age can't be empty")]
        #[Assert\NotNull(message: "Age can't be null")]
        #[Assert\Range(notInRangeMessage: "Range age is 12 - 50 years.", min: self::MINIMUM_AGE, max: self::MAXIMUM_AGE)]
        #[Assert\Regex(
            pattern: '/^\d+$/',
            message: 'Age should contain only digits.'
        )]
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
        #[Assert\Choice(
            choices: self::VALID_POSITIONS,
            message: 'The position is not a valid position. Allowed values are GOALKEEPER, DEFENDER, MIDFIELDER, FORWARD.',
        )]
        #[Assert\Regex(
            pattern: '/^[a-zA-Z]+$/',
            message: 'Position should contain only alphabetic characters.'
        )]
        public string $position,
    )
    {
    }
}
