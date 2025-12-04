<!-- Page title shown at the top of the page -->
<h1 class="page-title"><?= esc($title); ?></h1>

<!-- Wrapper for table styling -->
<div class="table-wrapper">

    <table>
        <!-- Caption is used for accessibility and screen readers -->
        <caption>Investments</caption>

        <thead>
        <tr>
            <!-- Table headings -->
            <th>ID</th>
            <th>Project</th>
            <th>Investor</th>
            <th>Amount</th>
            <th>Payment</th>
            <th>Date</th>
        </tr>
        </thead>

        <tbody>

        <!-- If investments list IS NOT empty -->
        <?php if (!empty($investments)): ?>

            <!-- Loop through each investment record -->
            <?php foreach ($investments as $i): ?>
                <tr>

                    <!-- Investment ID -->
                    <td><?= (int)$i['investment_id']; ?></td>

                    <!-- Project title associated with the investment -->
                    <td><?= esc($i['project_title']); ?></td>

                    <!-- Investor's name -->
                    <td><?= esc($i['investor_name']); ?></td>

                    <!-- Investment amount formatted to 2 decimal places -->
                    <td><?= number_format((float)$i['amount'], 2); ?></td>

                    <!-- Payment method (bank transfer, card, etc.) -->
                    <td><?= esc($i['payment_method']); ?></td>

                    <!-- Date the investment was made -->
                    <td><?= esc($i['investment_date']); ?></td>
                </tr>
            <?php endforeach; ?>

        <!-- If NO investments exist -->
        <?php else: ?>
            <tr>
                <td colspan="6">
                    No investments found.
                </td>
            </tr>
        <?php endif; ?>

        </tbody>
    </table>
</div>
