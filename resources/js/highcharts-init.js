import Highcharts from 'highcharts';
import Exporting     from 'highcharts/modules/exporting';
import ExportData    from 'highcharts/modules/export-data';
import Accessibility from 'highcharts/modules/accessibility';
import SeriesLabel   from 'highcharts/modules/series-label';

// ✅ Static imports — Vite resolves these at build time, no runtime resolution needed
// Initialize in correct dependency order
Exporting(Highcharts);
ExportData(Highcharts);     // depends on Exporting — must come after
Accessibility(Highcharts);
SeriesLabel(Highcharts);

Highcharts.setOptions({
    accessibility: {
        enabled: true
    }
});

window.Highcharts = Highcharts;

export default Highcharts;