<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Schemas;

final class JobPostingSchema
{
    public function __construct(
        private array $config = []
    ) {
    }

    public function build(array $data): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'JobPosting',
            'title' => $data['title'] ?? '',
            'description' => $data['description'] ?? '',
        ];

        // Date Posted
        if (!empty($data['datePosted'])) {
            $schema['datePosted'] = $data['datePosted'];
        }

        // Valid Through
        if (!empty($data['validThrough'])) {
            $schema['validThrough'] = $data['validThrough'];
        }

        // Employment Type
        if (!empty($data['employmentType'])) {
            $employmentTypes = is_array($data['employmentType']) 
                ? $data['employmentType'] 
                : [$data['employmentType']];
            $schema['employmentType'] = $employmentTypes;
        }

        // Hiring Organization
        if (!empty($data['hiringOrganization'])) {
            $org = $data['hiringOrganization'];
            if (is_string($org)) {
                $schema['hiringOrganization'] = [
                    '@type' => 'Organization',
                    'name' => $org,
                ];
            } elseif (is_array($org)) {
                $schema['hiringOrganization'] = array_merge(
                    ['@type' => 'Organization'],
                    $org
                );
            }

            // Add logo if available
            if (isset($org['logo'])) {
                $schema['hiringOrganization']['logo'] = $org['logo'];
            }

            // Add sameAs (website)
            if (isset($org['sameAs'])) {
                $schema['hiringOrganization']['sameAs'] = $org['sameAs'];
            }
        }

        // Job Location
        if (!empty($data['jobLocation'])) {
            $location = $data['jobLocation'];
            $jobLocation = [
                '@type' => 'Place',
            ];

            if (isset($location['address'])) {
                $address = $location['address'];
                $jobLocation['address'] = [
                    '@type' => 'PostalAddress',
                ];

                if (isset($address['streetAddress'])) {
                    $jobLocation['address']['streetAddress'] = $address['streetAddress'];
                }

                if (isset($address['addressLocality'])) {
                    $jobLocation['address']['addressLocality'] = $address['addressLocality'];
                }

                if (isset($address['addressRegion'])) {
                    $jobLocation['address']['addressRegion'] = $address['addressRegion'];
                }

                if (isset($address['postalCode'])) {
                    $jobLocation['address']['postalCode'] = $address['postalCode'];
                }

                if (isset($address['addressCountry'])) {
                    $jobLocation['address']['addressCountry'] = $address['addressCountry'];
                }
            }

            $schema['jobLocation'] = $jobLocation;
        }

        // Base Salary
        if (!empty($data['baseSalary'])) {
            $salary = $data['baseSalary'];
            $baseSalary = [
                '@type' => 'MonetaryAmount',
            ];

            if (isset($salary['currency'])) {
                $baseSalary['currency'] = $salary['currency'];
            }

            if (isset($salary['value'])) {
                $value = $salary['value'];
                if (is_array($value)) {
                    // Range
                    $baseSalary['value'] = [
                        '@type' => 'QuantitativeValue',
                    ];

                    if (isset($value['minValue'])) {
                        $baseSalary['value']['minValue'] = $value['minValue'];
                    }

                    if (isset($value['maxValue'])) {
                        $baseSalary['value']['maxValue'] = $value['maxValue'];
                    }

                    if (isset($value['value'])) {
                        $baseSalary['value']['value'] = $value['value'];
                    }
                } else {
                    $baseSalary['value'] = $value;
                }
            }

            $schema['baseSalary'] = $baseSalary;
        }

        // Job Benefits
        if (!empty($data['jobBenefits'])) {
            $benefits = is_array($data['jobBenefits']) 
                ? $data['jobBenefits'] 
                : [$data['jobBenefits']];
            $schema['jobBenefits'] = $benefits;
        }

        // Qualifications
        if (!empty($data['qualifications'])) {
            $qualifications = is_array($data['qualifications']) 
                ? $data['qualifications'] 
                : [$data['qualifications']];
            $schema['qualifications'] = $qualifications;
        }

        // Skills
        if (!empty($data['skills'])) {
            $skills = is_array($data['skills']) 
                ? $data['skills'] 
                : [$data['skills']];
            $schema['skills'] = $skills;
        }

        // Work Hours
        if (!empty($data['workHours'])) {
            $schema['workHours'] = $data['workHours'];
        }

        // Industry
        if (!empty($data['industry'])) {
            $schema['industry'] = $data['industry'];
        }

        // Experience Requirements
        if (!empty($data['experienceRequirements'])) {
            $schema['experienceRequirements'] = [
                '@type' => 'OccupationalExperienceRequirements',
            ];

            if (isset($data['experienceRequirements']['monthsOfExperience'])) {
                $schema['experienceRequirements']['monthsOfExperience'] = 
                    $data['experienceRequirements']['monthsOfExperience'];
            }
        }

        return $schema;
    }
}

