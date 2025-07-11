<?php
namespace App\Policies;
use App\Models\CalculationProject;
use App\Models\User;
class CalculationProjectPolicy
{
    public function view(User $user, CalculationProject $calculationProject): bool
    {
        return $user->id === $calculationProject->user_id;
    }
    public function update(User $user, CalculationProject $calculationProject): bool
    {
        return $user->id === $calculationProject->user_id;
    }
    public function delete(User $user, CalculationProject $calculationProject): bool
    {
        return $user->id === $calculationProject->user_id;
    }
}