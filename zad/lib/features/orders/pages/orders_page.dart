import 'package:flutter/material.dart';
import '../../../core/theme/app_theme.dart';

class OrdersPage extends StatelessWidget {
  const OrdersPage({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,

      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 0,
        centerTitle: true,
        title: const Text(
          "My Orders",
          style: TextStyle(
            color: AppTheme.primary,
            fontWeight: FontWeight.w600,
          ),
        ),
        iconTheme: const IconThemeData(color: AppTheme.primary),
      ),

      body: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: const [
            Icon(
              Icons.receipt_long,
              size: 64,
              color: AppTheme.greyLabel,
            ),
            SizedBox(height: 16),
            Text(
              "No orders yet",
              style: TextStyle(
                fontSize: 16,
                color: AppTheme.greyLabel,
              ),
            ),
            SizedBox(height: 6),
            Text(
              "Your orders will appear here",
              style: TextStyle(
                fontSize: 14,
                color: AppTheme.greyLabel,
              ),
            ),
          ],
        ),
      ),
    );
  }
}

