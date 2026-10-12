import 'package:flutter/material.dart';
import '../../../core/theme/app_theme.dart';

class HomeHeader extends StatelessWidget {
  final String userName;
  final String location;
  final int cartCount;
  final VoidCallback? onCartTap;

  const HomeHeader({
    super.key,
    this.userName = 'Guest',
    this.location = 'Your location',
    this.cartCount = 0,
    this.onCartTap,
  });

  @override
  Widget build(BuildContext context) {
    final textTheme = Theme.of(context).textTheme;

    return Row(
      crossAxisAlignment: CrossAxisAlignment.center,
      children: [
        // 👤 Avatar
        const CircleAvatar(
          radius: 18,
          backgroundColor: AppTheme.inputFill,
          child: Icon(Icons.person, color: AppTheme.greyLabel),
        ),

        const SizedBox(width: 8),

        // User info
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                'Hello, $userName',
                style: textTheme.bodyMedium?.copyWith(
                  fontWeight: FontWeight.w600,
                ),
              ),
              const SizedBox(height: 4),
              Row(
                children: [
                  const Icon(
                    Icons.location_on,
                    size: 16,
                    color: AppTheme.greyLabel,
                  ),
                  const SizedBox(width: 4),
                  Text(
                    location,
                    style: textTheme.bodySmall?.copyWith(
                      color: AppTheme.greyLabel,
                    ),
                  ),
                ],
              ),
            ],
          ),
        ),

        // 🛒 Cart
        Stack(
          clipBehavior: Clip.none,
          children: [
            IconButton(
              onPressed: onCartTap,
              icon: const Icon(Icons.shopping_bag_outlined),
            ),
            if (cartCount > 0)
              Positioned(
                right: 6,
                top: 6,
                child: Container(
                  padding: const EdgeInsets.all(4),
                  decoration: const BoxDecoration(
                    color: AppTheme.primary,
                    shape: BoxShape.circle,
                  ),
                  constraints: const BoxConstraints(
                    minWidth: 18,
                    minHeight: 18,
                  ),
                  child: Text(
                    cartCount.toString(),
                    style: const TextStyle(
                      color: Colors.white,
                      fontSize: 11,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ),
              ),
          ],
        ),

        const SizedBox(width: 6),

        // ☰ Menu (فتح الـ Drawer بالطريقة الصحيحة)
        Builder(
          builder: (ctx) => IconButton(
            icon: const Icon(Icons.menu),
            onPressed: () {
              Scaffold.of(ctx).openDrawer();
            },
          ),
        ),
      ],
    );
  }
}

