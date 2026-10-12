
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/storage/local_storage.dart';


final splashProvider = FutureProvider<bool>((ref) async {
  final token = await LocalStorage.getToken();
  return token != null;

});

