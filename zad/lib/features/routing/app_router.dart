import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:grad_project/features/cart/pages/cart_page.dart';

import 'package:grad_project/features/discovery/pages/discovery_page.dart';
import 'package:grad_project/features/orders/pages/order_success_page.dart';
import 'package:grad_project/features/profile/pages/profile_page.dart';
import '../cart/pages/checkout_page.dart';
import '../home/models/product_model.dart';
import '../home/pages/home_page.dart';

import '../product/pages/product_details_page.dart';
import '../splash/pages/splash_page.dart';
import '../auth/pages/welcome_page.dart';
import '../auth/pages/login_page.dart';
import '../auth/pages/register_page.dart';
import '../common/pages/offilne_page.dart';

import '../../shared/layouts/main_layout.dart';
import '../../shared/widgets/app_drawer.dart';

/// 🔑 Root navigator (مهم جدًا)
final _rootNavigatorKey = GlobalKey<NavigatorState>();

final globalRouterProvider = Provider<GoRouter>((ref) {
  return GoRouter(
    navigatorKey: _rootNavigatorKey,
    initialLocation: '/splash',
    routes: [
      /// -------------------------------
      /// 🟢 صفحات خارج الـ BottomNav
      /// -------------------------------

      GoRoute(
        path: '/splash',
        parentNavigatorKey: _rootNavigatorKey,
        builder: (_, __) => const SplashPage(),
      ),
      GoRoute(
        path: '/welcome',
        parentNavigatorKey: _rootNavigatorKey,
        builder: (_, __) => const WelcomePage(),
      ),
      GoRoute(
        path: '/login',
        parentNavigatorKey: _rootNavigatorKey,
        builder: (_, __) => const LoginPage(),
      ),
      GoRoute(
        path: '/register',
        parentNavigatorKey: _rootNavigatorKey,
        builder: (_, __) => const RegisterPage(),
      ),
      GoRoute(
        path: '/offline',
        parentNavigatorKey: _rootNavigatorKey,
        builder: (_, __) => const OfflinePage(),
      ),
      GoRoute(
        path: '/product/:id',
        builder: (context, state) {
          final product = state.extra as Product;
          return ProductDetailsPage(product: product);
        },
      ),
      GoRoute(
        path: '/cart',
        parentNavigatorKey: _rootNavigatorKey,
        builder: (_, __) => const CartPage(),
      ),
      GoRoute(
        path: '/checkout',
        parentNavigatorKey: _rootNavigatorKey,
        builder: (_, __) => const CheckoutPage(),
      ),
      GoRoute(
        path: '/order_success',
        parentNavigatorKey: _rootNavigatorKey,
        builder: (_, __) => const OrderSuccessPage(),
      ),



      /// -------------------------------
      /// 🔵 ShellRoute (مع BottomNav)
      /// -------------------------------
      ShellRoute(
        builder: (context, state, child) {
          return MainLayout(
            drawer: const AppDrawer(),
            child: child,
          );
        },
        routes: [
          GoRoute(
            path: '/home',
            builder: (_, __) => const HomePage(),
          ),
          GoRoute(
            path: '/discovery',
            builder: (_, __) => const DiscoverPage(),
          ),
          GoRoute(
            path: '/profile',
            builder: (_, __) => const ProfilePage(),
          ),
        ],
      ),
    ],
  );
});

