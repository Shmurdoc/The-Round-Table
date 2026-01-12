# Quick Reference: Crypto & Simulator Integration

## 🚀 Getting Started

### 1. Run Migration
```bash
cd "C:\wamp64\www\The round table\The Table"
php artisan migrate
```

### 2. Access Features

**Crypto Payments:**
- Deposit: http://localhost/crypto/deposit
- Withdrawal: http://localhost/crypto/withdrawal
- History: http://localhost/crypto/history

**Impact Simulator:**
- Main Interface: http://localhost/simulator

---

## 💰 Cryptocurrency Quick Guide

### Supported Coins
| Coin | Networks | Deposit Fee (Tier 1) | Withdrawal Fee |
|------|----------|---------------------|----------------|
| USDT | TRC20, ERC20, BEP20 | 1.0% | 0.5% + network |
| USDC | ERC20, Polygon, BEP20 | 1.0% | 0.5% + network |
| BTC | Bitcoin | 1.0% | 0.5% + 0.0005 BTC |
| ETH | Ethereum | 1.0% | 0.5% + 0.005 ETH |
| BNB | BSC | 1.0% | 0.5% + 0.001 BNB |

### Tier Benefits
```
Tier 1 (R3K-10K):   1.0% deposit fee
Tier 2 (R10K-25K):  0.75% deposit fee
Tier 3 (R25K-50K):  0.5% deposit fee
Tier 4 (R50K-75K):  0.35% deposit fee
Tier 5 (R75K-100K): 0.25% deposit fee + FREE withdrawals
```

### Withdrawal Limits & Processing
```
Tier 1: R50,000/day    → < R10K: 30 min
Tier 2: R100,000/day   → R10K-100K: 2 hours
Tier 3: R250,000/day   → > R100K: 24-48 hours (manual)
Tier 4: R500,000/day
Tier 5: R1,000,000/day
```

---

## 📊 Impact Simulator Features

✅ 4 Cohort Types (Utilization, Lease, Resale, Hybrid)
✅ Monte Carlo Simulation (10,000 iterations)
✅ Scenario Testing (Optimistic/Realistic/Pessimistic/Disaster)
✅ Portfolio Analysis
✅ Risk Scoring (1-10 scale)
✅ PDF Export
✅ Save Simulations

---

**Version**: 1.0 | **Updated**: Jan 7, 2026 | **Status**: ✅ Ready
