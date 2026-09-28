<?php

namespace App\Enums;

enum Role: string
{
    case Student = 'student';
    case Supervisor = 'supervisor';
    case Reviewer = 'reviewer';
    case UniversityLecturer = 'university_lecturer';
    case Operator = 'operator';
    case SuperOperator = 'super_operator';

    public function label(): string
    {
        return match ($this) {
            self::Student => 'Mahasiswa',
            self::Supervisor => 'Dosen Pembimbing',
            self::Reviewer => 'Reviewer',
            self::UniversityLecturer => 'Dosen Universitas',
            self::Operator => 'Operator',
            self::SuperOperator => 'Pimpinan PT',
        };
    }

    public function canManageAccounts(): bool
    {
        return $this === self::SuperOperator;
    }

    public function canManageControl(): bool
    {
        return in_array($this, [self::Operator, self::SuperOperator]);
    }

    public function dashboardRoute(): string
    {
        return match ($this) {
            self::Student => 'student.dashboard',
            self::Supervisor => 'supervisor.dashboard',
            self::Reviewer => 'reviewer.dashboard',
            self::UniversityLecturer => 'university.dashboard',
            self::Operator => 'operator.dashboard',
            self::SuperOperator => 'super-operator.dashboard',
        };
    }
}
