# Bluetooth Thermal Printer Setup Guide

## ✅ Implementation Complete

The student payment system now supports **direct Bluetooth thermal printer printing** using Web Bluetooth API.

---

## 📋 Requirements

### Browser Requirements
- **Chrome** (version 56+) or **Edge** (version 79+)
- **HTTPS connection required** (Web Bluetooth only works on secure origins)
- For local development: `localhost` is considered secure

### Printer Requirements
- Bluetooth thermal printer with **ESC/POS** command support
- Common models: 
  - POS-5805, POS-5802
  - Any 58mm/80mm ESC/POS Bluetooth printer
- Printer must be paired with your device first (via Windows Bluetooth settings)

---

## 🚀 How to Use

### Step 1: Record a Payment
1. Go to `/finance/student-payments`
2. Click **Record Payment**
3. Complete the 4-step payment form
4. After successful payment, you'll see the success screen

### Step 2: Print via Bluetooth
On the success screen, you'll see two print options:

1. **Print Receipt** (Blue button)
   - Opens browser print dialog
   - Works with any installed printer

2. **Bluetooth Printer** (Purple button) ⭐ NEW
   - Connects directly to Bluetooth thermal printer
   - Prints formatted receipt with ESC/POS commands

### Step 3: First-Time Setup
When you click **Bluetooth Printer** for the first time:
1. Browser will show a device selection popup
2. Select your Bluetooth thermal printer from the list
3. Click **Pair**
4. Receipt will print immediately
5. Next time, it will use the same printer automatically

---

## 📄 Receipt Format

The Bluetooth receipt includes:
```
================================
         ROSHS
  Robert Sobukwe High School
      Payment Receipt
      
       RCP-000123
================================

Student: John Doe
Date: 2026-01-13
Term: First Term 2026

Fees Paid For:
Tuition Fee ($500.00)

Payment Method: Cash
Reference: TXN123456

================================
      AMOUNT PAID
       $500.00
================================

      Thank You!
      
Printed: 1/13/2026, 8:30 AM
```

---

## 🔧 Troubleshooting

### "Web Bluetooth is not supported"
- **Solution**: Use Chrome or Edge browser
- Firefox and Safari don't support Web Bluetooth yet

### "HTTPS required"
- **Solution**: Access via HTTPS or localhost
- For production: Ensure SSL certificate is installed
- For local dev: Use `http://localhost` or `https://student_portal_roshs.test`

### Printer not appearing in device list
1. Ensure printer is turned ON
2. Pair printer in Windows Bluetooth settings first
3. Make sure printer is in pairing mode
4. Refresh the page and try again

### "Failed to print via Bluetooth"
1. Check printer has paper
2. Ensure printer is not connected to another device
3. Try disconnecting and reconnecting
4. Check printer battery/power

### Printer prints garbled text
- Your printer may use different Bluetooth service UUIDs
- Common alternatives:
  - Service: `49535343-fe7d-4ae5-8fa9-9fafd205e455`
  - Characteristic: `49535343-8841-43f4-a8d4-ecbe34729bb3`

---

## 🔐 Security Notes

- Web Bluetooth requires user permission (browser popup)
- Connection is only active during the session
- No printer data is stored or transmitted to servers
- All printing happens client-side in the browser

---

## 🛠️ Technical Details

### Bluetooth Service UUIDs Used
```javascript
Service UUID: 000018f0-0000-1000-8000-00805f9b34fb
Characteristic UUID: 00002af1-0000-1000-8000-00805f9b34fb
```

### ESC/POS Commands Implemented
- `ESC @` - Initialize printer
- `ESC a` - Text alignment (center/left)
- `ESC E` - Bold text
- `GS !` - Character size (double height)
- `GS V` - Paper cut
- Standard ASCII text printing

---

## 📱 Mobile Support

Web Bluetooth works on:
- ✅ Chrome for Android (version 56+)
- ✅ Edge for Android
- ❌ iOS Safari (not supported)
- ❌ Firefox Mobile (not supported)

---

## 🎯 Alternative: Regular Print

If Bluetooth printing doesn't work, use the **Print Receipt** button:
- Opens standard browser print dialog
- Works with any printer (USB, Network, Bluetooth via system)
- Requires printer to be installed in Windows

---

## 📞 Support

For issues or questions about Bluetooth printing:
1. Check printer is ESC/POS compatible
2. Verify browser and HTTPS requirements
3. Test with regular print first
4. Check printer manufacturer documentation

---

**Last Updated**: January 13, 2026
