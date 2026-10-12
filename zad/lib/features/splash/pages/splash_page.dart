import 'dart:async';
import 'package:flutter/material.dart';
import 'package:flutter_animate/flutter_animate.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:connectivity_plus/connectivity_plus.dart';

import '../../../core/theme/app_theme.dart';
import '../../../core/storage/local_storage.dart';
import '../../auth/providers/user_profile_provider.dart';

class SplashPage extends ConsumerStatefulWidget {
  const SplashPage({super.key});

  @override
  ConsumerState<SplashPage> createState() => _SplashPageState();
}

class _SplashPageState extends ConsumerState<SplashPage> {
  bool _hasInternet = true;
  bool _locked = true; // 🔒 هذا هو المفتاح
  StreamSubscription? _subscription;

  @override
  void initState() {
    super.initState();
    _startListening();
  }

  void _startListening() {
    _subscription = Connectivity()
        .onConnectivityChanged
        .listen((List<ConnectivityResult> results) {
      final online = !results.contains(ConnectivityResult.none);

      if (!mounted) return;

      setState(() {
        _hasInternet = online;
      });

      if (online && _locked) {
        _locked = false;
        _decideNavigation();
      }
    });

    // تحقق أولي
    Connectivity().checkConnectivity().then((results) {
      final online = !results.contains(ConnectivityResult.none);

      if (!mounted) return;

      setState(() {
        _hasInternet = online;
      });

      if (online && _locked) {
        _locked = false;
        _decideNavigation();
      }
    });
  }

  Future<void> _decideNavigation() async {
    await Future.delayed(const Duration(milliseconds: 600));

    final token = await LocalStorage.getToken();
    if (!mounted) return;

    if (token == null || token.isEmpty) {
      context.go('/welcome');
      return;
    }

    try {
      await ref.read(userProfileProvider.future);
      if (!mounted) return;
      context.go('/home');
    } catch (_) {
      await LocalStorage.clear();
      if (!mounted) return;
      context.go('/welcome');
    }
  }

  @override
  void dispose() {
    _subscription?.cancel();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: Center(
        child: !_hasInternet
            ? Column(
          mainAxisSize: MainAxisSize.min,
          children: const [
            Icon(Icons.wifi_off, size: 48, color: Colors.grey),
            SizedBox(height: 12),
            Text(
              'No Internet Connection',
              style: TextStyle(fontSize: 16),
            ),
            SizedBox(height: 6),
            Text(
              'Please check your connection',
              style: TextStyle(color: Colors.grey),
            ),
          ],
        )
            : Text(
          "ZAD",
          style: AppTheme.logoText!.copyWith(
            color: AppTheme.primary,
          ),
        )
            .animate()
            .fade(duration: 600.ms)
            .moveY(begin: 20, duration: 600.ms),
      ),
    );
  }
}

