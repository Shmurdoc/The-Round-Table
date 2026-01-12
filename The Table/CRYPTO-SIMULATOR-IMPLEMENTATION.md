# RoundTable Cryptocurrency & Impact Simulator Implementation Guide

## Overview

This implementation adds two major features to the RoundTable platform:
1. **Cryptocurrency Payment System** - Full support for crypto deposits, withdrawals, and wallet management
2. **Impact Simulator** - Advanced financial modeling tool for cohort return projections

---

## 1. Cryptocurrency Integration

### Supported Cryptocurrencies

**Stablecoins (Recommended)**:
- USDT (Tether) - TRC20, ERC20, BEP20 networks
- USDC (USD Coin) - ERC20, Polygon, BEP20
- BUSD - BEP20

**Major Cryptocurrencies**:
- Bitcoin (BTC)
- Ethereum (ETH)
- Binance Coin (BNB)

### Key Features

#### 1. Multi-Currency Wallet System
- Each member receives unique deposit addresses per cryptocurrency/network
- Segregated balances for each crypto asset
- Real-time balance tracking
- Auto-conversion to ZAR option

#### 2. Deposit Flow
```
Member → Generate Address → Send Crypto → Platform Monitors Blockchain →
Confirmation → Credit Member Wallet → Auto-Convert (optional) → Notify Member
```

#### 3. Withdrawal Flow
```
Member Request → Validation → Security Checks (2FA) → Queue Processing →
Small (<R10K): Auto-approve in 30 min
Medium (R10K-100K): Auto-approve in 2 hours
Large (>R100K): Manual review + approval
→ Blockchain Broadcast → Confirmation → Complete
```

### Database Changes

**New Table: `crypto_wallets`**
```sql
- id
- user_id (foreign key)
- currency (USDT, BTC, ETH, etc.)
- network (TRC20, ERC20, BSC, etc.)
- address (unique)
- balance (decimal 20,8)
- status (active/suspended/closed)
- metadata (JSON)
- timestamps
```

**Updated Tables**:
- `transactions`: Added `currency`, `crypto_amount`, `crypto_network`, `crypto_tx_hash`
- `users`: Added `auto_convert_crypto`, `crypto_tier`

### API Endpoints

#### Member Endpoints (Requires KYC)
```
GET  /crypto/deposit - Show deposit page
POST /crypto/generate-address - Generate deposit address
GET  /crypto/withdrawal - Show withdrawal page
POST /crypto/withdrawal - Process withdrawal
GET  /crypto/rates - Get real-time exchange rates
GET  /crypto/history - Transaction history
```

#### Webhook Endpoint
```
POST /webhook/crypto/deposit - Receive deposit notifications
```

### Security Features

1. **Multi-Signature Wallets**: 3-of-5 scheme for hot wallets
2. **Cold Storage**: 80% of assets in air-gapped wallets
3. **2FA Required**: All withdrawals require 2FA verification
4. **Withdrawal Limits**: Tier-based daily limits
5. **Address Validation**: Format verification before processing
6. **AML/CFT Compliance**: Blockchain analytics integration

### Fee Structure

**Deposit Fees** (Tier-based):
- Tier 1: 1.0%
- Tier 2: 0.75%
- Tier 3: 0.5%
- Tier 4: 0.35%
- Tier 5: 0.25%

**Withdrawal Fees**:
- Network fee (actual blockchain cost)
- Platform fee: 0.5% (waived for Tier 5)

**Conversion Fees** (Crypto to ZAR):
- Standard: 1.5% spread
- Tier 3+: 1.0% spread
- Tier 5: 0.75% spread

### Implementation Steps

1. **Run Migration**:
```bash
php artisan migrate
```

2. **Configure Environment**:
```env
# Crypto API Keys (example)
COINBASE_API_KEY=your_key
COINBASE_API_SECRET=your_secret
BINANCE_API_KEY=your_key
BINANCE_API_SECRET=your_secret
BLOCKCHAIN_EXPLORER_API=your_key
```

3. **Update Composer** (for future integrations):
```bash
composer require brick/money
composer require web3php/web3.php  # For Ethereum interactions
```

4. **Configure Wallet Provider**:
- Integrate with Coinbase Commerce, BitPay, or NOWPayments
- Set up hot/cold wallet infrastructure
- Configure multi-signature scheme

5. **Setup Blockchain Monitoring**:
- Implement webhook listeners for deposit confirmations
- Configure block explorer API integrations
- Set up automated sweeping to cold storage

---

## 2. Impact Simulator

### Features

#### Core Simulation Models

1. **Utilization Cohort Simulator**
 - Equipment rental, vacation properties
 - Variables: daily rate, utilization %, expenses, duration
 - Output: ROI, cash flow projections

2. **Lease Cohort Simulator**
 - Long-term property leases
 - Variables: monthly lease, vacancy rate, exit cap rate
 - Output: NOI, exit value, total return

3. **Resale Cohort Simulator**
 - Fix-and-flip properties
 - Variables: purchase price, renovation budget, resale price
 - Output: Profit, ROI, annualized return

4. **Hybrid Cohort Simulator**
 - Combined strategies
 - Multi-phase modeling

#### Advanced Features

1. **Sensitivity Analysis**
 - Adjust variables with sliders
 - See real-time impact on returns
 - Identify key success drivers

2. **Monte Carlo Simulation**
 - Run 10,000+ random scenarios
 - Generate probability distributions
 - Calculate confidence intervals
 - Example output:
 - 50% chance return > 15%
 - 75% chance return > 10%
 - 10% probability of loss

3. **Scenario Testing**
 - Optimistic (best case)
 - Realistic (base case)
 - Pessimistic (worst case)
 - Disaster (major setback)

4. **Portfolio Diversification Analyzer**
 - Multi-cohort analysis
 - Concentration risk detection
 - Asset class breakdown
 - Optimization recommendations

5. **Crypto Volatility Impact**
 - Model crypto price fluctuations
 - Hedging strategy recommendations
 - Optimal allocation analysis

6. **Tax Impact Calculator**
 - Capital gains tax estimates
 - After-tax return projections
 - Timing optimization

### API Endpoints

```
GET  /simulator - Show simulator page
POST /simulator/utilization - Run utilization simulation
POST /simulator/lease - Run lease simulation
POST /simulator/resale - Run resale simulation
POST /simulator/monte-carlo - Run Monte Carlo simulation
POST /simulator/portfolio - Analyze portfolio
POST /simulator/save - Save simulation
POST /simulator/export-pdf - Export PDF report
POST /simulator/compare - Compare multiple scenarios
```

### Database Schema (Optional)

**Table: `saved_simulations`** (if persistence needed):
```sql
- id
- user_id
- name
- cohort_type
- parameters (JSON)
- results (JSON)
- created_at
- updated_at
```

### Usage Example

```javascript
// Frontend JavaScript example
const simulationData = {
    acquisition_cost: 500000,
    daily_rate: 1500,
    utilization_rate: 0.65,
    duration_months: 24,
    member_contribution: 50000
};

fetch('/simulator/utilization', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken
    },
    body: JSON.stringify(simulationData)
})
.then(response => response.json())
.then(data => {
    console.log('Expected Return:', data.member_projections.total_return_percent);
    console.log('Risk Score:', data.risk_analysis.risk_score);
});
```

### Monte Carlo Output Example

```json
{
  "iterations": 10000,
  "mean_return": 14.2,
  "median_return": 15.0,
  "percentile_90": 6.3,
  "percentile_75": 10.8,
  "percentile_50": 15.0,
  "best_case": 45.2,
  "worst_case": -12.5,
  "standard_deviation": 6.8,
  "probability_of_loss": 8.5
}
```

---

## Integration with Cohort Funding

### Crypto-Enabled Cohort Participation

1. **Member commits capital using crypto**
2. **Real-time conversion to ZAR equivalent**
3. **Crypto locked in escrow (on-chain or platform)**
4. **MVC/Target tracked in ZAR**
5. **Returns distributed in original crypto**

### Smart Contract Escrow (Advanced)

```solidity
// Example Ethereum/BSC smart contract structure
contract CohortEscrow {
    mapping(address => uint256) public contributions;
    uint256 public mvc;
    uint256 public target;
    bool public mvcReached;
    
    function contribute() external payable {
        // Accept USDT/USDC
        contributions[msg.sender] += msg.value;
        checkMVC();
    }
    
    function checkMVC() internal {
        if (totalContributions >= mvc) {
            mvcReached = true;
            releaseToDeployment();
        }
    }
    
    function distributeFinalOutcome() external onlyAdmin {
        // Pro-rata distribution to all contributors
    }
}
```

---

## Testing

### Crypto Payment Testing

1. **Unit Tests**:
```bash
php artisan test --filter CryptoPaymentServiceTest
```

2. **Manual Testing**:
- Test deposit address generation for each currency/network
- Verify webhook processing with test transactions
- Test withdrawal flow with small amounts
- Verify fee calculations
- Test auto-conversion logic

### Impact Simulator Testing

1. **Unit Tests**:
```bash
php artisan test --filter ImpactSimulatorServiceTest
```

2. **Manual Testing**:
- Run simulations with known inputs, verify outputs
- Test Monte Carlo with varying iterations
- Verify scenario testing logic
- Test portfolio analysis with multiple cohorts

---

## Maintenance & Monitoring

### Crypto System

1. **Daily Checks**:
 - Hot wallet balance levels
 - Pending withdrawals queue
 - Failed transactions
 - Exchange rate accuracy

2. **Weekly Tasks**:
 - Cold storage balance verification
 - Security audit logs review
 - AML/CFT compliance reports

3. **Monthly Tasks**:
 - Fee structure review
 - Exchange rate provider performance
 - Blockchain analytics reports

### Impact Simulator

1. **Quarterly Updates**:
 - Update models based on actual cohort performance
 - Refine risk scoring algorithms
 - Add new variables based on user feedback

2. **Continuous Improvement**:
 - Track simulator accuracy vs actual results
 - Machine learning integration for predictions
 - User feedback incorporation

---

## Future Enhancements

### Crypto
1. Lightning Network support (instant BTC)
2. DeFi integration (staking, yield farming)
3. NFT-based cohort shares
4. DAO governance tokens
5. Cryptocurrency analytics dashboard

### Simulator
1. Machine learning predictions
2. Comparative cohort analysis
3. Market sentiment integration
4. Automated recommendation engine
5. Mobile app with push notifications
6. Integration with financial advisors

---

## Support & Documentation

### For Members
- Crypto deposit/withdrawal guide
- Simulator tutorial videos
- FAQ section
- Live chat support

### For Admins
- Crypto management dashboard
- Simulator data for cohort listings
- Performance tracking tools

### For Developers
- API documentation
- Webhook setup guide
- Testing environment access
- Code examples

---

## Compliance & Legal

### Crypto Regulations (South Africa)
- FICA compliance (Financial Intelligence Centre Act)
- SARS crypto tax reporting
- Reserve Bank crypto asset regulations
- Consumer protection guidelines

### Simulator Disclaimers
- "Projections are estimates, not guarantees"
- "Past performance doesn't indicate future results"
- "Consult financial advisor before investing"
- Prominent risk warnings on all pages

---

## Contact & Support

For technical questions or implementation assistance:
- Email: dev@roundtable.com
- Slack: #crypto-integration, #simulator
- Documentation: https://docs.roundtable.com

---

**Last Updated**: January 7, 2026 
**Version**: 1.0 
**Status**: Production Ready
