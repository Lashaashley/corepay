<?php

namespace App\Services;

use App\Models\Agents;
use App\Models\Payhouse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SummaryDataService
{
    /**
     * Get all summary data with caching
     */
    public function getSummaryData($allowedPayroll)
    {
        $cacheKey = 'summary_data_' . md5(json_encode($allowedPayroll));
        $cacheTime = 300; // 5 minutes

        return Cache::remember($cacheKey, $cacheTime, function () use ($allowedPayroll) {
            return [
                'periodOptions' => $this->getPeriodOptions(),
                'pnameOptions' => $this->getPnameOptions(),
                'statutoryOptions' => $this->getStatutoryOptions(),
                'snameOptions' => $this->getStaffOptions($allowedPayroll)
            ];
        });
    }

    /**
     * Get period options
     */
    private function getPeriodOptions()
    {
        try {
            $periods = Payhouse::select('month', 'year')
                ->distinct()
                ->orderByRaw("year DESC, FIELD(month, 'December', 'November', 'October', 'September', 'August', 'July', 'June', 'May', 'April', 'March', 'February', 'January')")
                ->get();

            $options = [];
            foreach ($periods as $period) {
                $options[] = [
                    'value' => $period->month . $period->year,
                    'text' => $period->month . ' ' . $period->year
                ];
            }

            return $options;
        } catch (\Exception $e) {
            Log::error('Error fetching periods', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Get pname (items) options
     */
    private function getPnameOptions()
    {
        try {
            $pnames = Payhouse::select('pname')
                ->whereNotNull('itemcode')
                ->where('itemcode', '!=', '')
                ->distinct()
                ->orderBy('pname')
                ->get();

            $options = [];
            foreach ($pnames as $item) {
                $options[] = [
                    'value' => $item->pname,
                    'text' => $item->pname
                ];
            }

            return $options;
        } catch (\Exception $e) {
            Log::error('Error fetching pnames', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Get statutory options
     */
    private function getStatutoryOptions()
    {
        try {
            $statutoryItems = ['Pension', 'NSSF', 'NHIF', 'SHIF', 'PAYE', 'Housing Levy'];
            
            $statutory = Payhouse::select('pname')
                ->whereIn('pname', $statutoryItems)
                ->distinct()
                ->orderBy('pname')
                ->get();

            $options = [];
            foreach ($statutory as $item) {
                $options[] = [
                    'value' => $item->pname,
                    'text' => $item->pname
                ];
            }

            return $options;
        } catch (\Exception $e) {
            Log::error('Error fetching statutory items', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Get staff options filtered by allowed payroll
     */
    private function getStaffOptions($allowedPayroll)
    {
        try {
            if (empty($allowedPayroll)) {
                return [];
            }

            $staff = Agents::select(
                    'tblemployees.emp_id as WorkNo',
                    DB::raw("CONCAT(tblemployees.FirstName, ' ', tblemployees.LastName) as fullname")
                )
                ->join('registration', 'tblemployees.emp_id', '=', 'registration.empid')
                ->join('payhouse', 'tblemployees.emp_id', '=', 'payhouse.WorkNo')
                ->whereIn('registration.payrolty', $allowedPayroll)
                ->where('tblemployees.Status', 'ACTIVE')
                ->distinct()
                ->orderBy('tblemployees.emp_id')
                ->get();

            $options = [];
            foreach ($staff as $member) {
                $options[] = [
                    'value' => $member->WorkNo,
                    'text' => $member->WorkNo . ' - ' . $member->fullname
                ];
            }

            return $options;
        } catch (\Exception $e) {
            Log::error('Error fetching staff', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Clear summary data cache
     */
    public function clearCache($allowedPayroll = null)
    {
        if ($allowedPayroll) {
            $cacheKey = 'summary_data_' . md5(json_encode($allowedPayroll));
            Cache::forget($cacheKey);
        } else {
            // Clear all summary caches
            Cache::tags(['summary_data'])->flush();
        }
    }
}